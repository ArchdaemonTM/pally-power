<?php
/**
 * PALADIN PROFILE v3 — Database & Utilities
 * LUMINOUS Engine · Production Build
 * Compatible: PHP 8.1+ / MariaDB 10.4+ / IONOS Shared Hosting
 */
declare(strict_types=1);

$configPath = __DIR__ . '/../config.php';
if (!file_exists($configPath)) {
    if (str_contains($_SERVER['REQUEST_URI'] ?? '', '/api/')) {
        http_response_code(503);
        header('Content-Type: application/json');
        echo json_encode(['error'=>true,'message'=>'Not installed. Visit /install/']);
        exit;
    }
    header('Location: /install/');
    exit;
}
require_once $configPath;

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
            ]);
        } catch (PDOException $e) {
            if (defined('APP_ENV') && APP_ENV === 'development') throw $e;
            http_response_code(503);
            header('Content-Type: application/json');
            echo json_encode(['error'=>true,'message'=>'Database unavailable']);
            exit;
        }
    }
    return $pdo;
}

function uuid4(): string {
    $d = random_bytes(16);
    $d[6] = chr(ord($d[6]) & 0x0f | 0x40);
    $d[8] = chr(ord($d[8]) & 0x3f | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($d), 4));
}

function generateSlug(int $len = 8): string {
    $c = 'abcdefghijkmnpqrstuvwxyz23456789';
    $s = '';
    for ($i = 0; $i < $len; $i++) $s .= $c[random_int(0, strlen($c)-1)];
    return $s;
}

function generateEditToken(): string {
    return bin2hex(random_bytes(32));
}

function jsonResponse(mixed $data, int $status = 200): never {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function jsonError(string $msg, int $status = 400): never {
    jsonResponse(['error'=>true, 'message'=>$msg], $status);
}

function getJsonBody(): array {
    $raw = file_get_contents('php://input');
    if (empty($raw)) return [];
    $data = json_decode($raw, true);
    if (json_last_error() !== JSON_ERROR_NONE) jsonError('Invalid JSON: '.json_last_error_msg(), 400);
    return $data;
}

function sanitize(string $input, int $max = 500): string {
    $input = trim(strip_tags($input));
    return function_exists('mb_substr') ? mb_substr($input, 0, $max, 'UTF-8') : substr($input, 0, $max);
}

function handleCors(): void {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    if (!empty($origin) && defined('CORS_ORIGINS') && in_array($origin, CORS_ORIGINS, true)) {
        header("Access-Control-Allow-Origin: $origin");
    }
    header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, X-Edit-Token');
    header('Access-Control-Max-Age: 86400');
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
}

function checkRateLimit(string $action = 'default', int $maxPerHour = 10): bool {
    try {
        $pdo = db();
        $ipHash = hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? '0') . $action);
        $pdo->exec("CREATE TABLE IF NOT EXISTS `rate_limits` (
            `ip_hash` VARCHAR(64), `action` VARCHAR(32),
            `hit_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX `idx_lookup` (`ip_hash`,`action`,`hit_at`)
        ) ENGINE=InnoDB");
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM rate_limits WHERE ip_hash=? AND action=? AND hit_at>DATE_SUB(NOW(),INTERVAL 1 HOUR)");
        $stmt->execute([$ipHash, $action]);
        if ((int)$stmt->fetchColumn() >= $maxPerHour) return false;
        $pdo->prepare("INSERT INTO rate_limits (ip_hash,action) VALUES (?,?)")->execute([$ipHash,$action]);
        if (random_int(1,20)===1) $pdo->exec("DELETE FROM rate_limits WHERE hit_at<DATE_SUB(NOW(),INTERVAL 2 HOUR)");
        return true;
    } catch (PDOException) { return true; }
}
