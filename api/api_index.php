<?php
/**
 * PALADIN PROFILE v5 — API Router
 * LUMINOUS Engine · Production Build
 * Single entry point: /api/api_index.php (Repo-Wide Unique Name Convention)
 * 
 * Routes:
 *   GET  /api/?action=gamedata           → all orders, alignments, attributes, callings, specs, talents, feats
 *   GET  /api/?action=character&id=UUID  → single character (public or with edit token)
 *   GET  /api/?action=character&slug=XX  → single character by public slug
 *   POST /api/?action=character          → create character
 *   PUT  /api/?action=character&id=UUID  → update character (requires X-Edit-Token header)
 *   POST /api/?action=levelup&id=UUID   → level up with journal entry
 *   POST /api/?action=suggestion         → submit community suggestion
 *   GET  /api/?action=export&id=UUID     → export character as JSON
 *   POST /api/?action=import             → import character from JSON
 *   GET  /api/?action=gallery            → public character gallery
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
handleCors();

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

try {
    match ($action) {
        'gamedata'       => handleGameData(),
        'character'      => handleCharacter($method),
        'levelup'        => handleLevelUp(),
        'suggestion'     => handleSuggestion(),
        'export'         => handleExport(),
        'import'         => handleImport(),
        'gallery'        => handleGallery(),
        default          => jsonError('Unknown action', 404),
    };
} catch (PDOException $e) {
    if (APP_ENV === 'development') {
        jsonError('Database error: ' . $e->getMessage(), 500);
    }
    jsonError('Internal server error', 500);
}

// ═══════════════════════════════════════════════════════════════
// GAME DATA — returns ALL expandable game content
// ═══════════════════════════════════════════════════════════════
function handleGameData(): void {
    $pdo = db();
    
    $orders = $pdo->query("SELECT * FROM orders WHERE is_active=1 ORDER BY sort_order")->fetchAll();
    $alignments = $pdo->query("SELECT * FROM alignments ORDER BY sort_order")->fetchAll();
    $attributes = $pdo->query("SELECT * FROM attributes WHERE is_active=1 ORDER BY sort_order")->fetchAll();
    $callings = $pdo->query("SELECT * FROM callings WHERE is_active=1 ORDER BY order_id, name")->fetchAll();
    $specializations = $pdo->query("SELECT * FROM specializations WHERE is_active=1 ORDER BY category, name")->fetchAll();
    $talentTrees = $pdo->query("SELECT * FROM talent_trees WHERE is_active=1 ORDER BY sort_order")->fetchAll();
    $talentNodes = $pdo->query("SELECT * FROM talent_nodes WHERE is_active=1 ORDER BY tree_id, tier")->fetchAll();
    $feats = $pdo->query("SELECT * FROM feats WHERE is_active=1 ORDER BY level_req")->fetchAll();
    
    // Group callings by order
    $callingsByOrder = [];
    foreach ($callings as $c) {
        $callingsByOrder[$c['order_id']][] = $c;
    }
    
    // Group specs by category
    $specsByCategory = [];
    foreach ($specializations as $s) {
        $specsByCategory[$s['category']][] = $s;
    }
    
    // Group talent nodes by tree
    $nodesByTree = [];
    foreach ($talentNodes as $n) {
        $nodesByTree[$n['tree_id']][] = $n;
    }
    
    jsonResponse([
        'orders'          => $orders,
        'alignments'      => $alignments,
        'attributes'      => $attributes,
        'callings'        => $callingsByOrder,
        'callingsFlat'    => $callings,
        'specializations' => $specsByCategory,
        'specializationsFlat' => $specializations,
        'talentTrees'     => $talentTrees,
        'talentNodes'     => $nodesByTree,
        'talentNodesFlat' => $talentNodes,
        'feats'           => $feats,
        'specCategories'  => array_keys($specsByCategory),
    ]);
}

// ═══════════════════════════════════════════════════════════════
// CHARACTER CRUD
// ═══════════════════════════════════════════════════════════════
function handleCharacter(string $method): void {
    match ($method) {
        'GET'  => getCharacter(),
        'POST' => createCharacter(),
        'PUT'  => updateCharacter(),
        default => jsonError('Method not allowed', 405),
    };
}

function getCharacter(): void {
    $pdo = db();
    $id = $_GET['id'] ?? null;
    $slug = $_GET['slug'] ?? null;
    
    if ($slug) {
        $stmt = $pdo->prepare("SELECT * FROM characters WHERE share_slug = ? AND is_public = 1");
        $stmt->execute([$slug]);
    } elseif ($id) {
        $stmt = $pdo->prepare("SELECT * FROM characters WHERE id = ?");
        $stmt->execute([$id]);
    } else {
        jsonError('id or slug required', 400);
    }
    
    $char = $stmt->fetch();
    if (!$char) jsonError('Character not found', 404);
    
    // Strip edit_token from public responses unless authorized
    $editToken = $_SERVER['HTTP_X_EDIT_TOKEN'] ?? '';
    if (!hash_equals($char['edit_token'], $editToken)) {
        unset($char['edit_token']);
        $char['is_owner'] = false;
    } else {
        $char['is_owner'] = true;
    }
    
    // Fetch related data
    $char['attribute_values'] = $pdo->prepare(
        "SELECT ca.*, a.name, a.description FROM character_attributes ca 
         JOIN attributes a ON ca.attribute_id = a.id 
         WHERE ca.character_id = ? ORDER BY a.sort_order"
    );
    $char['attribute_values']->execute([$char['id']]);
    $char['attribute_values'] = $char['attribute_values']->fetchAll();
    
    $char['talent_values'] = $pdo->prepare(
        "SELECT ct.*, tt.name, tt.icon FROM character_talents ct
         JOIN talent_trees tt ON ct.tree_id = tt.id
         WHERE ct.character_id = ?"
    );
    $char['talent_values']->execute([$char['id']]);
    $char['talent_values'] = $char['talent_values']->fetchAll();
    
    $char['feat_progress'] = $pdo->prepare(
        "SELECT cf.*, f.name, f.description, f.level_req FROM character_feats cf
         JOIN feats f ON cf.feat_id = f.id
         WHERE cf.character_id = ? ORDER BY f.level_req"
    );
    $char['feat_progress']->execute([$char['id']]);
    $char['feat_progress'] = $char['feat_progress']->fetchAll();
    
    $char['journal'] = $pdo->prepare(
        "SELECT * FROM level_journal WHERE character_id = ? ORDER BY to_level ASC"
    );
    $char['journal']->execute([$char['id']]);
    $char['journal'] = $char['journal']->fetchAll();
    
    // Decode build_json — null the raw string to prevent double-encoding in jsonResponse
    if ($char['build_json']) {
        $char['build_data'] = json_decode($char['build_json'], true);
        $char['build_json'] = null;
    }
    
    jsonResponse(['character' => $char]);
}

function createCharacter(): void {
    $pdo = db();
    $data = getJsonBody();
    
    $id = uuid4();
    $editToken = generateEditToken();
    $slug = null;
    
    // Generate unique slug if public
    $isPublic = !empty($data['is_public']);
    if ($isPublic) {
        for ($i = 0; $i < 10; $i++) {
            $slug = generateSlug();
            $check = $pdo->prepare("SELECT 1 FROM characters WHERE share_slug = ?");
            $check->execute([$slug]);
            if (!$check->fetch()) break;
            $slug = null;
        }
        if (!$slug) $slug = generateSlug(12);
    }
    
    // Build JSON snapshot of full character state
    $buildJson = json_encode([
        'attributes'      => $data['attributes'] ?? [],
        'talents'         => $data['talents'] ?? [],
        'custom_calling'  => $data['calling_custom'] ?? null,
        'custom_spec'     => $data['specialization_custom'] ?? null,
        'custom_attrs'    => $data['custom_attributes'] ?? [],
        'level_plan'      => $data['level_plan'] ?? [],
    ]);
    
    $stmt = $pdo->prepare(
        "INSERT INTO characters 
         (id, share_slug, name, title, oath_source, oath_name, oath_statement,
          order_id, alignment_id, calling_id, calling_custom,
          specialization_id, specialization_custom, is_public, edit_token, build_json)
         VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
    );
    
    $stmt->execute([
        $id,
        $slug,
        sanitize($data['name'] ?? 'Unnamed Paladin', 100),
        sanitize($data['title'] ?? '', 150),
        sanitize($data['oath_source'] ?? '', 100),
        sanitize($data['oath_name'] ?? '', 150),
        sanitize($data['oath_statement'] ?? '', 2000),
        $data['order_id'] ?? null,
        $data['alignment_id'] ?? null,
        !empty($data['calling_id']) ? (int)$data['calling_id'] : null,
        sanitize($data['calling_custom'] ?? '', 100) ?: null,
        !empty($data['specialization_id']) ? (int)$data['specialization_id'] : null,
        sanitize($data['specialization_custom'] ?? '', 100) ?: null,
        $isPublic ? 1 : 0,
        $editToken,
        $buildJson,
    ]);
    
    // Save attribute allocations
    if (!empty($data['attributes']) && is_array($data['attributes'])) {
        $attrStmt = $pdo->prepare(
            "INSERT INTO character_attributes (character_id, attribute_id, base_value, current_value) 
             VALUES (?, ?, ?, ?)"
        );
        foreach ($data['attributes'] as $attrId => $val) {
            $val = max(1, min(99, (int)$val));
            $attrStmt->execute([$id, sanitize($attrId, 32), $val, $val]);
        }
    }
    
    // Save talent allocations
    if (!empty($data['talents']) && is_array($data['talents'])) {
        $talStmt = $pdo->prepare(
            "INSERT INTO character_talents (character_id, tree_id, points_allocated) VALUES (?, ?, ?)"
        );
        foreach ($data['talents'] as $treeId => $pts) {
            $talStmt->execute([$id, sanitize($treeId, 32), max(0, min(15, (int)$pts))]);
        }
    }
    
    jsonResponse([
        'success'    => true,
        'id'         => $id,
        'edit_token' => $editToken,
        'slug'       => $slug,
        'share_url'  => $slug ? APP_URL . '/p/' . $slug : null,
        'message'    => 'Paladin created. Save your edit_token — it is your key to modify this character.',
    ], 201);
}

function updateCharacter(): void {
    $pdo = db();
    $id = $_GET['id'] ?? '';
    $editToken = $_SERVER['HTTP_X_EDIT_TOKEN'] ?? '';
    
    if (!$id || !$editToken) jsonError('id and X-Edit-Token required', 400);
    
    $stmt = $pdo->prepare("SELECT edit_token FROM characters WHERE id = ?");
    $stmt->execute([$id]);
    $char = $stmt->fetch();
    
    if (!$char || !hash_equals($char['edit_token'], $editToken)) {
        jsonError('Unauthorized', 403);
    }
    
    $data = getJsonBody();
    
    $buildJson = json_encode([
        'attributes'      => $data['attributes'] ?? [],
        'talents'         => $data['talents'] ?? [],
        'custom_calling'  => $data['calling_custom'] ?? null,
        'custom_spec'     => $data['specialization_custom'] ?? null,
        'custom_attrs'    => $data['custom_attributes'] ?? [],
        'level_plan'      => $data['level_plan'] ?? [],
    ]);
    
    $update = $pdo->prepare(
        "UPDATE characters SET 
         name=?, title=?, oath_source=?, oath_name=?, oath_statement=?,
         order_id=?, alignment_id=?, calling_id=?, calling_custom=?,
         specialization_id=?, specialization_custom=?, is_public=?, build_json=?
         WHERE id=?"
    );
    
    $isPublic = !empty($data['is_public']);
    
    // Generate slug if switching to public
    if ($isPublic) {
        $slugCheck = $pdo->prepare("SELECT share_slug FROM characters WHERE id=?");
        $slugCheck->execute([$id]);
        $existing = $slugCheck->fetch();
        if (empty($existing['share_slug'])) {
            $slug = generateSlug();
            $pdo->prepare("UPDATE characters SET share_slug=? WHERE id=?")->execute([$slug, $id]);
        }
    }
    
    $update->execute([
        sanitize($data['name'] ?? 'Unnamed Paladin', 100),
        sanitize($data['title'] ?? '', 150),
        sanitize($data['oath_source'] ?? '', 100),
        sanitize($data['oath_name'] ?? '', 150),
        sanitize($data['oath_statement'] ?? '', 2000),
        $data['order_id'] ?? null,
        $data['alignment_id'] ?? null,
        !empty($data['calling_id']) ? (int)$data['calling_id'] : null,
        sanitize($data['calling_custom'] ?? '', 100) ?: null,
        !empty($data['specialization_id']) ? (int)$data['specialization_id'] : null,
        sanitize($data['specialization_custom'] ?? '', 100) ?: null,
        $isPublic ? 1 : 0,
        $buildJson,
        $id,
    ]);
    
    // Update attributes (delete and reinsert)
    $pdo->prepare("DELETE FROM character_attributes WHERE character_id=?")->execute([$id]);
    if (!empty($data['attributes']) && is_array($data['attributes'])) {
        $attrStmt = $pdo->prepare(
            "INSERT INTO character_attributes (character_id, attribute_id, base_value, current_value)
             VALUES (?, ?, ?, ?)"
        );
        foreach ($data['attributes'] as $attrId => $val) {
            $val = max(1, min(99, (int)$val));
            $attrStmt->execute([$id, sanitize($attrId, 32), $val, $val]);
        }
    }
    
    // Update talents
    $pdo->prepare("DELETE FROM character_talents WHERE character_id=?")->execute([$id]);
    if (!empty($data['talents']) && is_array($data['talents'])) {
        $talStmt = $pdo->prepare(
            "INSERT INTO character_talents (character_id, tree_id, points_allocated) VALUES (?, ?, ?)"
        );
        foreach ($data['talents'] as $treeId => $pts) {
            $talStmt->execute([$id, sanitize($treeId, 32), max(0, min(15, (int)$pts))]);
        }
    }
    
    jsonResponse(['success' => true, 'message' => 'Character updated']);
}

// ═══════════════════════════════════════════════════════════════
// LEVEL UP — The Dream Tracker
// ═══════════════════════════════════════════════════════════════
function handleLevelUp(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonError('POST required', 405);
    
    $pdo = db();
    $id = $_GET['id'] ?? '';
    $editToken = $_SERVER['HTTP_X_EDIT_TOKEN'] ?? '';
    
    if (!$id || !$editToken) jsonError('id and X-Edit-Token required', 400);
    
    $stmt = $pdo->prepare("SELECT id, edit_token, current_level FROM characters WHERE id=?");
    $stmt->execute([$id]);
    $char = $stmt->fetch();
    
    if (!$char || !hash_equals($char['edit_token'], $editToken)) {
        jsonError('Unauthorized', 403);
    }
    
    if ((int)$char['current_level'] >= 50) {
        jsonError('Already at maximum level (50). You are a Paragon.', 400);
    }
    
    $data = getJsonBody();
    $journalEntry = sanitize($data['journal_entry'] ?? '', 5000);
    
    if (strlen($journalEntry) < 20) {
        jsonError('Journal entry must describe the real growth that justifies this level-up (at least 20 characters)', 400);
    }
    
    $fromLevel = (int)$char['current_level'];
    $toLevel = $fromLevel + 1;
    
    // Record journal entry
    $jStmt = $pdo->prepare(
        "INSERT INTO level_journal (character_id, from_level, to_level, journal_entry, mentor_name, mentor_type, evidence_notes)
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    $jStmt->execute([
        $id, $fromLevel, $toLevel, $journalEntry,
        sanitize($data['mentor_name'] ?? '', 100) ?: null,
        sanitize($data['mentor_type'] ?? '', 50) ?: null,
        sanitize($data['evidence_notes'] ?? '', 2000) ?: null,
    ]);
    
    // Increment level
    $pdo->prepare("UPDATE characters SET current_level=? WHERE id=?")->execute([$toLevel, $id]);
    
    $response = [
        'success'    => true,
        'new_level'  => $toLevel,
        'message'    => "Level up! You are now Level $toLevel.",
    ];
    
    // Check for feat unlock
    if ($toLevel % 5 === 0) {
        $featStmt = $pdo->prepare("SELECT * FROM feats WHERE level_req = ?");
        $featStmt->execute([$toLevel]);
        $feat = $featStmt->fetch();
        if ($feat) {
            $pdo->prepare(
                "INSERT IGNORE INTO character_feats (character_id, feat_id, justification) VALUES (?, ?, ?)"
            )->execute([$id, $feat['id'], $journalEntry]);
            $response['feat_unlocked'] = $feat;
            $response['message'] .= " FEAT UNLOCKED: {$feat['name']}!";
        }
    }
    
    if (in_array($toLevel, [10, 20, 30, 40, 50])) {
        $response['capstone'] = true;
        $response['message'] .= ' ★ CAPSTONE MILESTONE!';
    }
    
    $response['talent_points_gained'] = ($toLevel === 1) ? 5 : 2;
    
    jsonResponse($response);
}

// ═══════════════════════════════════════════════════════════════
// SUGGESTIONS — Community-submitted additions
// ═══════════════════════════════════════════════════════════════
function handleSuggestion(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonError('POST required', 405);
    
    // Rate limit: 10 suggestions per hour per IP
    if (!checkRateLimit('suggestion', 10)) {
        jsonError('Rate limit exceeded. Please wait before submitting another suggestion.', 429);
    }
    
    $data = getJsonBody();
    
    $type = $data['suggestion_type'] ?? '';
    $validTypes = ['attribute','calling','specialization','talent','order','feat','other'];
    if (!in_array($type, $validTypes, true)) {
        jsonError('Invalid suggestion_type. Valid: ' . implode(', ', $validTypes), 400);
    }
    
    $name = sanitize($data['suggested_name'] ?? '', 150);
    if (strlen($name) < 2) jsonError('suggested_name is required (min 2 chars)', 400);
    
    $pdo = db();
    $stmt = $pdo->prepare(
        "INSERT INTO suggestions 
         (suggestion_type, suggested_name, suggested_category, description, submitter_name, submitter_email, submitter_character_id)
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([
        $type,
        $name,
        sanitize($data['suggested_category'] ?? '', 100) ?: null,
        sanitize($data['description'] ?? '', 2000) ?: null,
        sanitize($data['submitter_name'] ?? '', 100) ?: null,
        sanitize($data['submitter_email'] ?? '', 200) ?: null,
        sanitize($data['submitter_character_id'] ?? '', 36) ?: null,
    ]);
    
    jsonResponse(['success' => true, 'message' => 'Suggestion submitted for review. Thank you!'], 201);
}

// ═══════════════════════════════════════════════════════════════
// EXPORT / IMPORT — JSON character portability
// ═══════════════════════════════════════════════════════════════
function handleExport(): void {
    $pdo = db();
    $id = $_GET['id'] ?? '';
    if (!$id) jsonError('id required', 400);
    
    $stmt = $pdo->prepare("SELECT * FROM characters WHERE id=?");
    $stmt->execute([$id]);
    $char = $stmt->fetch();
    if (!$char) jsonError('Character not found', 404);
    
    // Remove sensitive data
    unset($char['edit_token']);
    
    // Fetch all related
    $char['attribute_values'] = $pdo->prepare(
        "SELECT attribute_id, base_value, current_value, justification FROM character_attributes WHERE character_id=?"
    );
    $char['attribute_values']->execute([$id]);
    $char['attribute_values'] = $char['attribute_values']->fetchAll();
    
    $char['talent_values'] = $pdo->prepare(
        "SELECT tree_id, points_allocated FROM character_talents WHERE character_id=?"
    );
    $char['talent_values']->execute([$id]);
    $char['talent_values'] = $char['talent_values']->fetchAll();
    
    $char['journal'] = $pdo->prepare(
        "SELECT from_level, to_level, journal_entry, mentor_name, mentor_type, evidence_notes, created_at 
         FROM level_journal WHERE character_id=? ORDER BY to_level"
    );
    $char['journal']->execute([$id]);
    $char['journal'] = $char['journal']->fetchAll();
    
    $export = [
        '_format'    => 'paladin-profile-v3',
        '_version'   => APP_VERSION,
        '_exported'  => date('c'),
        '_engine'    => 'LUMINOUS',
        'character'  => $char,
    ];
    
    header('Content-Disposition: attachment; filename="paladin-' . ($char['share_slug'] ?? $id) . '.json"');
    jsonResponse($export);
}

function handleImport(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonError('POST required', 405);
    
    $data = getJsonBody();
    
    if (!in_array($data['_format'] ?? '', ['paladin-profile-v2', 'paladin-profile-v3'], true)) {
        jsonError('Invalid import format. Expected paladin-profile-v2 or paladin-profile-v3', 400);
    }
    
    $charData = $data['character'] ?? null;
    if (!$charData) jsonError('No character data found in import', 400);
    
    // Create as a new character (new ID, new edit token)
    $importPayload = [
        'name'                  => $charData['name'] ?? 'Imported Paladin',
        'title'                 => $charData['title'] ?? '',
        'oath_source'           => $charData['oath_source'] ?? '',
        'oath_name'             => $charData['oath_name'] ?? '',
        'oath_statement'        => $charData['oath_statement'] ?? '',
        'order_id'              => $charData['order_id'] ?? null,
        'alignment_id'          => $charData['alignment_id'] ?? null,
        'calling_id'            => $charData['calling_id'] ?? null,
        'calling_custom'        => $charData['calling_custom'] ?? '',
        'specialization_id'     => $charData['specialization_id'] ?? null,
        'specialization_custom' => $charData['specialization_custom'] ?? '',
        'is_public'             => false,
        'attributes'            => [],
        'talents'               => [],
    ];
    
    // Reconstruct attributes
    if (!empty($charData['attribute_values'])) {
        foreach ($charData['attribute_values'] as $av) {
            $importPayload['attributes'][$av['attribute_id']] = (int)($av['base_value'] ?? 8);
        }
    }
    
    // Reconstruct talents
    if (!empty($charData['talent_values'])) {
        foreach ($charData['talent_values'] as $tv) {
            $importPayload['talents'][$tv['tree_id']] = (int)($tv['points_allocated'] ?? 0);
        }
    }
    
    // Hijack the JSON body and call create
    // We do this by directly encoding and re-reading
    $_rawOverride = json_encode($importPayload);
    
    // Inline create logic to avoid circular dependency
    $pdo = db();
    $id = uuid4();
    $editToken = generateEditToken();
    
    $buildJson = json_encode($charData['build_data'] ?? $charData['build_json'] ?? []);
    
    $stmt = $pdo->prepare(
        "INSERT INTO characters 
         (id, name, title, oath_source, oath_name, oath_statement,
          order_id, alignment_id, calling_id, calling_custom,
          specialization_id, specialization_custom, current_level, is_public, edit_token, build_json)
         VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
    );
    $stmt->execute([
        $id,
        sanitize($importPayload['name'], 100),
        sanitize($importPayload['title'], 150),
        sanitize($importPayload['oath_source'], 100),
        sanitize($importPayload['oath_name'], 150),
        sanitize($importPayload['oath_statement'], 2000),
        $importPayload['order_id'],
        $importPayload['alignment_id'],
        $importPayload['calling_id'] ?: null,
        sanitize($importPayload['calling_custom'], 100) ?: null,
        $importPayload['specialization_id'] ?: null,
        sanitize($importPayload['specialization_custom'], 100) ?: null,
        (int)($charData['current_level'] ?? 1),
        0,
        $editToken,
        $buildJson,
    ]);
    
    // Import attributes
    if (!empty($importPayload['attributes'])) {
        $attrStmt = $pdo->prepare(
            "INSERT INTO character_attributes (character_id, attribute_id, base_value, current_value) VALUES (?,?,?,?)"
        );
        foreach ($importPayload['attributes'] as $aid => $val) {
            $attrStmt->execute([$id, sanitize($aid, 32), $val, $val]);
        }
    }
    
    // Import talents
    if (!empty($importPayload['talents'])) {
        $talStmt = $pdo->prepare(
            "INSERT INTO character_talents (character_id, tree_id, points_allocated) VALUES (?,?,?)"
        );
        foreach ($importPayload['talents'] as $tid => $pts) {
            $talStmt->execute([$id, sanitize($tid, 32), max(0, min(15, (int)$pts))]);
        }
    }
    
    jsonResponse([
        'success'    => true,
        'id'         => $id,
        'edit_token' => $editToken,
        'message'    => 'Character imported successfully. Save your edit_token!',
    ], 201);
}

// ═══════════════════════════════════════════════════════════════
// GALLERY — Public character browser
// ═══════════════════════════════════════════════════════════════
function handleGallery(): void {
    $pdo = db();
    $page = max(1, (int)($_GET['page'] ?? 1));
    $limit = min(50, max(1, (int)($_GET['limit'] ?? 20)));
    $offset = ($page - 1) * $limit;
    $orderFilter = $_GET['order'] ?? '';
    
    $where = "is_public = 1";
    $params = [];
    
    if ($orderFilter) {
        $where .= " AND order_id = ?";
        $params[] = $orderFilter;
    }
    
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM characters WHERE $where");
    $countStmt->execute($params);
    $total = (int)$countStmt->fetchColumn();
    
    $params[] = $limit;
    $params[] = $offset;
    $stmt = $pdo->prepare(
        "SELECT c.id, c.share_slug, c.name, c.title, c.oath_name, c.order_id, c.alignment_id,
                c.current_level, c.created_at, c.updated_at,
                o.name as order_name, o.color_hex as order_color,
                a.name as alignment_name
         FROM characters c
         LEFT JOIN orders o ON c.order_id = o.id
         LEFT JOIN alignments a ON c.alignment_id = a.id
         WHERE $where
         ORDER BY c.updated_at DESC
         LIMIT ? OFFSET ?"
    );
    $stmt->execute($params);
    
    jsonResponse([
        'characters' => $stmt->fetchAll(),
        'total'      => $total,
        'page'       => $page,
        'pages'      => ceil($total / $limit),
    ]);
}
