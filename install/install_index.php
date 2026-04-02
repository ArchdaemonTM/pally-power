<?php
/**
 * ═══════════════════════════════════════════════════════════════
 * PALADIN PROFILE v5 — Web Installer
 * LUMINOUS Engine · Repo-Wide Unique File: install/install_index.php
 * Route: /install/ → served by root .htaccess rewrite
 * 
 * USAGE:
 *   1. Upload all files to your IONOS subdomain root
 *   2. Navigate to https://pally-profile.goldhatconsulting.com/install/
 *   3. Enter your MariaDB credentials
 *   4. Click Install
 *   5. The installer creates config.php, runs schema, seeds data
 *   6. DELETE the /install/ directory after successful installation
 * 
 * REQUIREMENTS:
 *   - PHP 8.1+ (tested on 8.4)
 *   - MariaDB 10.4+ or MySQL 8.0+
 *   - PDO extension enabled
 *   - Write access to parent directory (for config.php)
 * ═══════════════════════════════════════════════════════════════
 */

declare(strict_types=1);
error_reporting(E_ALL);

// Security: prevent running if config already exists
$configPath = dirname(__DIR__) . '/config.php';
$alreadyInstalled = file_exists($configPath);

$step = $_POST['step'] ?? $_GET['step'] ?? 'check';
$errors = [];
$success = [];
$dbConnected = false;

// ─── STEP: Pre-flight checks ───
function runChecks(): array {
    $checks = [];
    
    // PHP Version
    $phpVer = PHP_VERSION;
    $phpOk = version_compare($phpVer, '8.1.0', '>=');
    $checks['php_version'] = [
        'label' => "PHP Version: $phpVer",
        'ok' => $phpOk,
        'note' => $phpOk ? 'Compatible' : 'Requires PHP 8.1+',
    ];
    
    // PDO
    $pdoOk = extension_loaded('pdo') && extension_loaded('pdo_mysql');
    $checks['pdo'] = [
        'label' => 'PDO MySQL Extension',
        'ok' => $pdoOk,
        'note' => $pdoOk ? 'Loaded' : 'Required: enable pdo_mysql in php.ini',
    ];
    
    // JSON
    $jsonOk = extension_loaded('json');
    $checks['json'] = [
        'label' => 'JSON Extension',
        'ok' => $jsonOk,
        'note' => $jsonOk ? 'Loaded' : 'Required: enable json extension',
    ];
    
    // mbstring
    $mbOk = extension_loaded('mbstring');
    $checks['mbstring'] = [
        'label' => 'mbstring Extension',
        'ok' => $mbOk,
        'note' => $mbOk ? 'Loaded' : 'Recommended for UTF-8 handling',
    ];
    
    // Write access for config.php
    $parentDir = dirname(__DIR__);
    $writable = is_writable($parentDir);
    $checks['writable'] = [
        'label' => 'Config directory writable',
        'ok' => $writable,
        'note' => $writable ? "Can write to $parentDir" : "Cannot write to $parentDir — you may need to create config.php manually",
    ];
    
    // Schema file exists
    $schemaPath = __DIR__ . '/schema.sql';
    $schemaOk = file_exists($schemaPath);
    $checks['schema'] = [
        'label' => 'Schema file',
        'ok' => $schemaOk,
        'note' => $schemaOk ? 'Found: install/schema.sql' : 'Missing: install/schema.sql',
    ];
    
    // Check key application files
    $requiredFiles = [
        '../index.html', '../api/api_index.php', '../includes/db.php',
        '../p/p_index.php', '../orders/orders_index.php',
        '../patches/patches_index.php',
        '../public/css/paladin.css', '../public/css/heartsong.css',
        '../public/js/heartsong.js', '../.htaccess',
    ];
    $allFilesOk = true;
    $missingFiles = [];
    foreach ($requiredFiles as $f) {
        if (!file_exists(__DIR__ . '/' . $f)) {
            $allFilesOk = false;
            $missingFiles[] = $f;
        }
    }
    $checks['app_files'] = [
        'label' => 'Application files',
        'ok' => $allFilesOk,
        'note' => $allFilesOk ? 'All 10 required files present' : 'Missing: ' . implode(', ', $missingFiles),
    ];
    
    // mod_rewrite (best-effort check)
    $rewriteOk = function_exists('apache_get_modules') ? in_array('mod_rewrite', apache_get_modules()) : true;
    $checks['mod_rewrite'] = [
        'label' => 'Apache mod_rewrite',
        'ok' => $rewriteOk,
        'note' => $rewriteOk ? 'Available (or cannot verify on this host — IONOS typically has it)' : 'May not be available',
    ];
    
    return $checks;
}

// ─── STEP: Test DB connection ───
function testConnection(string $host, string $name, string $user, string $pass): ?PDO {
    try {
        $dsn = "mysql:host=$host;charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        
        // Try to select/create the database
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `$name`");
        
        return $pdo;
    } catch (PDOException $e) {
        return null;
    }
}

// ─── STEP: Run schema ───
function runSchema(PDO $pdo): array {
    $schemaPath = __DIR__ . '/schema.sql';
    $sql = file_get_contents($schemaPath);
    
    $results   = [];
    $tableCount = 0;
    $insertCount = 0;
    $errorCount  = 0;

    // Proper statement splitter: respects single-quoted strings and line comments
    $statements = [];
    $current    = '';
    $inString   = false;
    $quoteChar  = '';
    $len        = strlen($sql);

    for ($i = 0; $i < $len; $i++) {
        $ch = $sql[$i];

        // Toggle string context
        if (!$inString && ($ch === "'" || $ch === '"')) {
            $inString  = true;
            $quoteChar = $ch;
            $current  .= $ch;
            continue;
        }
        if ($inString) {
            if ($ch === '\\') {           // escaped character inside string
                $current .= $ch . ($sql[$i+1] ?? '');
                $i++;
                continue;
            }
            if ($ch === $quoteChar) {
                $inString = false;
            }
            $current .= $ch;
            continue;
        }

        // Outside a string: handle line comments
        if ($ch === '-' && ($sql[$i+1] ?? '') === '-') {
            // skip to end of line
            while ($i < $len && $sql[$i] !== "\n") $i++;
            continue;
        }

        // Statement terminator
        if ($ch === ';') {
            $stmt = trim($current);
            if ($stmt !== '') {
                $upper = strtoupper($stmt);
                // Skip CREATE DATABASE and USE statements (already handled)
                if (!str_starts_with($upper, 'CREATE DATABASE') && !str_starts_with($upper, 'USE ')) {
                    $statements[] = $stmt;
                }
            }
            $current = '';
            continue;
        }

        $current .= $ch;
    }
    // Catch any trailing statement without a final semicolon
    $stmt = trim($current);
    if ($stmt !== '') {
        $upper = strtoupper($stmt);
        if (!str_starts_with($upper, 'CREATE DATABASE') && !str_starts_with($upper, 'USE ')) {
            $statements[] = $stmt;
        }
    }

    foreach ($statements as $stmt) {
        try {
            $pdo->exec($stmt);
            if (stripos($stmt, 'CREATE TABLE') !== false) {
                $tableCount++;
            } elseif (stripos($stmt, 'INSERT INTO') !== false) {
                $insertCount++;
            }
        } catch (PDOException $e) {
            if ($e->getCode() === '42S01' || str_contains($e->getMessage(), 'already exists')) {
                $results[] = ['warn', 'Table already exists (skipped): ' . substr($stmt, 0, 60)];
            } elseif (str_contains($e->getMessage(), 'Duplicate entry')) {
                $results[] = ['warn', 'Duplicate data (skipped): ' . substr($stmt, 0, 60)];
            } else {
                $results[] = ['error', $e->getMessage() . ' — SQL: ' . substr($stmt, 0, 80)];
                $errorCount++;
            }
        }
    }

    $results[] = ['info', "Schema complete: $tableCount tables created, $insertCount data inserts, $errorCount errors"];

    $tables    = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    $results[] = ['info', 'Tables in database: ' . implode(', ', $tables)];

    return $results;
}
// ─── STEP: Write config.php ───
function writeConfig(string $host, string $name, string $user, string $pass): bool {
    $configPath = dirname(__DIR__) . '/config.php';
    
    $content = <<<PHP
<?php
/**
 * PALADIN PROFILE v3 — Configuration
 * Generated by installer on %DATE%
 */

// Database
define('DB_HOST', '%HOST%');
define('DB_NAME', '%NAME%');
define('DB_USER', '%USER%');
define('DB_PASS', '%PASS%');
define('DB_CHARSET', 'utf8mb4');

// Application
define('APP_NAME', 'Paladin Profile Generator');
define('APP_VERSION', '3.0.0');
define('APP_URL', '%URL%');
define('APP_ENV', 'production');

// Security
define('CORS_ORIGINS', [
    '%URL%',
    'http://localhost:8080',
]);

// Limits
define('SUGGESTION_RATE_LIMIT', 10);
define('SUGGESTION_COOLDOWN_SECONDS', 60);
define('MAX_IMPORT_SIZE_KB', 512);
PHP;
    
    $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http');
    $url .= '://' . ($_SERVER['HTTP_HOST'] ?? 'pally-profile.goldhatconsulting.com');
    $url = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/');
    $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'pally-profile.goldhatconsulting.com');
    
    $content = str_replace(
        ['%DATE%', '%HOST%', '%NAME%', '%USER%', '%PASS%', '%URL%'],
        [date('Y-m-d H:i:s'), $host, $name, $user, addslashes($pass), $url],
        $content
    );
    
    return file_put_contents($configPath, $content) !== false;
}

// ─── PROCESS FORM SUBMISSION ───
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 'install') {
    $dbHost = trim($_POST['db_host'] ?? '');
    $dbName = trim($_POST['db_name'] ?? '');
    $dbUser = trim($_POST['db_user'] ?? '');
    $dbPass = $_POST['db_pass'] ?? '';
    
    if (!$dbHost || !$dbName || !$dbUser) {
        $errors[] = 'All database fields except password are required.';
    } else {
        // Test connection
        $pdo = testConnection($dbHost, $dbName, $dbUser, $dbPass);
        if (!$pdo) {
            $errors[] = "Could not connect to MariaDB at $dbHost with user $dbUser. Check credentials.";
        } else {
            $dbConnected = true;
            $success[] = "Connected to MariaDB at $dbHost";
            
            // Run schema
            $schemaResults = runSchema($pdo);
            foreach ($schemaResults as [$level, $msg]) {
                if ($level === 'error') $errors[] = $msg;
                else $success[] = $msg;
            }
            
            // Write config
            if (empty($errors)) {
                if (writeConfig($dbHost, $dbName, $dbUser, $dbPass)) {
                    $success[] = 'config.php created successfully';
                } else {
                    $errors[] = 'Could not write config.php — create it manually (see below)';
                }
            }
        }
    }
}

$checks = runChecks();
$allChecksPass = !array_filter($checks, fn($c) => !$c['ok'] && $c['label'] !== 'Apache mod_rewrite');

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Install — Paladin Profile v5</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative:wght@700;900&family=Cinzel:wght@400;700&family=EB+Garamond:wght@400;500&family=Source+Sans+3:wght@400;600;700&family=JetBrains+Mono:wght@400&display=swap" rel="stylesheet">
<style>
:root{--ink:#0f0c08;--parchment:#f7f2e6;--gold:#c8961a;--gold-bright:#e8b830;--gold-deep:#8b6410;--forest:#1a3d1e;--iron:#1e1c18;--crimson:#8b1414;--silver:#c8c4bc;--r:8px}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Source Sans 3',sans-serif;font-size:16px;line-height:1.7;color:var(--ink);background:var(--parchment);min-height:100vh}
.wrap{max-width:720px;margin:0 auto;padding:2rem 1.5rem}
h1{font-family:'Cinzel Decorative',serif;font-size:1.8rem;color:var(--gold-deep);text-align:center;margin-bottom:.25rem}
h2{font-family:'Cinzel',serif;font-size:1.2rem;color:var(--iron);margin:2rem 0 1rem;border-bottom:1px solid rgba(200,150,26,.2);padding-bottom:.5rem}
.subtitle{text-align:center;font-family:'EB Garamond',serif;font-size:1rem;color:#7a6850;margin-bottom:2rem}
.check-list{list-style:none;margin:1rem 0}
.check-item{padding:.5rem .75rem;border-radius:var(--r);margin-bottom:.4rem;display:flex;align-items:center;gap:.75rem;font-size:.88rem}
.check-item.ok{background:rgba(26,107,60,.08);border:1px solid rgba(26,107,60,.2)}
.check-item.fail{background:rgba(139,20,20,.08);border:1px solid rgba(139,20,20,.2)}
.check-icon{font-size:1.1rem;flex-shrink:0}
.check-label{font-weight:600;color:var(--iron)}
.check-note{color:#7a6850;font-size:.82rem;margin-left:auto}
.form-group{margin-bottom:1rem}
.form-label{font-size:.78rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--gold-deep);display:block;margin-bottom:.3rem}
.form-input{width:100%;padding:.5rem .75rem;border:1px solid rgba(200,150,26,.3);border-radius:4px;font-family:'JetBrains Mono',monospace;font-size:.88rem;background:rgba(255,255,255,.7)}
.form-input:focus{outline:none;border-color:var(--gold);box-shadow:0 0 0 3px rgba(200,150,26,.15)}
.form-hint{font-size:.75rem;color:#7a6850;margin-top:.2rem}
.btn{display:inline-block;padding:.7rem 2rem;border-radius:4px;font-weight:700;font-size:.88rem;letter-spacing:.06em;cursor:pointer;transition:all .2s;border:none}
.btn-primary{background:var(--gold);color:var(--ink)}
.btn-primary:hover{background:var(--gold-bright);transform:translateY(-1px)}
.btn-primary:disabled{opacity:.5;cursor:not-allowed;transform:none}
.btn-danger{background:var(--crimson);color:white}
.msg{padding:.75rem 1rem;border-radius:var(--r);margin-bottom:.5rem;font-size:.85rem}
.msg-ok{background:rgba(26,107,60,.1);border:1px solid rgba(26,107,60,.3);color:var(--forest)}
.msg-err{background:rgba(139,20,20,.1);border:1px solid rgba(139,20,20,.3);color:var(--crimson)}
.msg-warn{background:rgba(200,150,26,.1);border:1px solid rgba(200,150,26,.3);color:var(--gold-deep)}
.config-box{background:var(--iron);color:var(--silver);border-radius:var(--r);padding:1rem 1.25rem;font-family:'JetBrains Mono',monospace;font-size:.78rem;line-height:1.6;overflow-x:auto;margin:1rem 0;white-space:pre-wrap;word-break:break-all}
.footer{text-align:center;margin-top:3rem;padding-top:1.5rem;border-top:1px solid rgba(200,150,26,.15);font-size:.78rem;color:#7a6850}
.installed-banner{background:var(--forest);color:white;padding:1.5rem;border-radius:var(--r);text-align:center;margin:2rem 0}
.installed-banner h3{font-family:'Cinzel',serif;margin-bottom:.5rem}
.security-warning{background:rgba(139,20,20,.1);border:2px solid var(--crimson);border-radius:var(--r);padding:1.25rem;margin:1.5rem 0}
.security-warning strong{color:var(--crimson)}
</style>
</head>
<body>
<div class="wrap">

<h1>⚜ Paladin Profile v5</h1>
<div class="subtitle">LUMINOUS Engine Installer · Rose Ministries · GoldHat Consulting</div>

<?php if ($alreadyInstalled && empty($success)): ?>
  <div class="installed-banner">
    <h3>Already Installed</h3>
    <p>config.php exists. The application appears to be installed.</p>
    <p style="margin-top:.75rem"><a href="/" style="color:var(--gold-bright);font-weight:700">→ Go to Paladin Profile</a></p>
  </div>
  <div class="security-warning">
    <strong>Security Notice:</strong> Delete the <code>/install/</code> directory now. Leaving the installer accessible is a security risk.
  </div>
<?php endif; ?>

<!-- Results -->
<?php foreach ($success as $msg): ?>
  <div class="msg msg-ok">✓ <?= htmlspecialchars($msg) ?></div>
<?php endforeach; ?>
<?php foreach ($errors as $msg): ?>
  <div class="msg msg-err">✗ <?= htmlspecialchars($msg) ?></div>
<?php endforeach; ?>

<?php if (!empty($success) && empty($errors)): ?>
  <div class="installed-banner">
    <h3>Installation Complete!</h3>
    <p>Your Paladin Profile is ready.</p>
    <p style="margin-top:.75rem"><a href="/" style="color:var(--gold-bright);font-weight:700;font-size:1.1rem">→ Launch Paladin Profile</a></p>
  </div>
  <div class="security-warning">
    <strong>IMPORTANT:</strong> Delete the entire <code>/install/</code> directory immediately. This installer contains database credentials in form history and should not remain accessible.
  </div>
<?php else: ?>

<!-- Pre-flight Checks -->
<h2>Pre-Flight Checks</h2>
<ul class="check-list">
<?php foreach ($checks as $check): ?>
  <li class="check-item <?= $check['ok'] ? 'ok' : 'fail' ?>">
    <span class="check-icon"><?= $check['ok'] ? '✓' : '✗' ?></span>
    <span class="check-label"><?= htmlspecialchars($check['label']) ?></span>
    <span class="check-note"><?= htmlspecialchars($check['note']) ?></span>
  </li>
<?php endforeach; ?>
</ul>

<?php if (!$alreadyInstalled): ?>
<!-- Database Configuration -->
<h2>Database Configuration</h2>
<p style="font-size:.88rem;color:#5a5040;margin-bottom:1rem">Enter your IONOS MariaDB credentials. Find these in your IONOS hosting panel under Databases.</p>

<form method="POST" action="?step=install">
  <input type="hidden" name="step" value="install">
  
  <div class="form-group">
    <label class="form-label">Database Host</label>
    <input class="form-input" name="db_host" value="<?= htmlspecialchars($_POST['db_host'] ?? 'localhost') ?>" required>
    <div class="form-hint">IONOS: usually db1234567890.hosting-data.io or similar. Check your IONOS panel.</div>
  </div>
  
  <div class="form-group">
    <label class="form-label">Database Name</label>
    <input class="form-input" name="db_name" value="<?= htmlspecialchars($_POST['db_name'] ?? 'pally_profile') ?>" required>
    <div class="form-hint">Create this database in your IONOS panel first, or the installer will attempt to create it.</div>
  </div>
  
  <div class="form-group">
    <label class="form-label">Database User</label>
    <input class="form-input" name="db_user" value="<?= htmlspecialchars($_POST['db_user'] ?? '') ?>" required>
  </div>
  
  <div class="form-group">
    <label class="form-label">Database Password</label>
    <input class="form-input" name="db_pass" type="password" value="">
  </div>
  
  <div style="margin-top:1.5rem">
    <button type="submit" class="btn btn-primary" <?= !$allChecksPass ? 'disabled title="Fix pre-flight checks first"' : '' ?>>
      ⚔ Install Paladin Profile v3
    </button>
  </div>
</form>
<?php endif; ?>

<?php endif; ?>

<div class="footer">
  <p>Paladin Profile v5.0.0 · LUMINOUS Engine · © 2026 David William Sylvester</p>
  <p>GoldHat™ 98925168 · ArchDaemon™ 98940257 · Rose Ministries Ordained</p>
</div>

</div>
</body>
</html>
