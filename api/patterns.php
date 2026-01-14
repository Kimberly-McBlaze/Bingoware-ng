<?php
/**
 * Patterns API Endpoint
 * Handles JSON requests for pattern CRUD/list/reset operations
 * 
 * Methods:
 * - GET  /api/patterns.php           - List all patterns
 * - GET  /api/patterns.php?id=XXX    - Get single pattern
 * - POST /api/patterns.php           - Create/Update pattern
 * - POST /api/patterns.php (delete_id) - Delete pattern
 * - POST /api/patterns.php (reset_to_default) - Reset to defaults
 */

// Load bootstrap
include_once(__DIR__ . "/../include/bootstrap.php");

// Set JSON content type
header('Content-Type: application/json');

// Get HTTP method
$method = $_SERVER['REQUEST_METHOD'];

// List all patterns
if ($method === 'GET' && !isset($_GET['id'])) {
    echo json_encode(['success' => true, 'patterns' => load_patterns()]);
    exit;
}

// Get single pattern
if ($method === 'GET' && isset($_GET['id'])) {
    $pattern_id = validate_pattern_id($_GET['id']);
    if (!$pattern_id) {
        echo json_encode(['success' => false, 'error' => 'Invalid pattern ID']);
        exit;
    }
    
    $pattern = get_pattern_by_id($pattern_id);
    if ($pattern) {
        echo json_encode(['success' => true, 'pattern' => $pattern]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Pattern not found']);
    }
    exit;
}

// Delete pattern - Check this BEFORE create/update to avoid false matches
if ($method === 'POST' && isset($_POST['delete_id'])) {
    $pattern_id = validate_pattern_id($_POST['delete_id']);
    if (!$pattern_id) {
        echo json_encode(['success' => false, 'error' => 'Invalid pattern ID']);
        exit;
    }
    
    $result = delete_pattern($pattern_id);
    echo json_encode($result);
    exit;
}

// Reset patterns to default - Check this BEFORE create/update to avoid false matches
if ($method === 'POST' && isset($_POST['reset_to_default'])) {
    $result = reset_patterns_to_default();
    echo json_encode($result);
    exit;
}

// Create pattern
if ($method === 'POST' && (!isset($_POST['id']) || empty($_POST['id']))) {
    $name = validate_string($_POST['name'] ?? '', 50);
    $description = validate_string($_POST['description'] ?? '', 200);
    $grid = validate_json($_POST['grid'] ?? '[]', []);
    $enabled = validate_bool($_POST['enabled'] ?? false);
    
    if (empty($name)) {
        echo json_encode(['success' => false, 'error' => 'Pattern name is required']);
        exit;
    }
    
    $result = create_pattern($name, $description, $grid, $enabled);
    echo json_encode($result);
    exit;
}

// Update pattern
if ($method === 'POST' && isset($_POST['id']) && !empty($_POST['id'])) {
    $id = validate_pattern_id($_POST['id']);
    if (!$id) {
        echo json_encode(['success' => false, 'error' => 'Invalid pattern ID']);
        exit;
    }
    
    $name = validate_string($_POST['name'] ?? '', 50);
    $description = validate_string($_POST['description'] ?? '', 200);
    $grid = isset($_POST['grid']) ? validate_json($_POST['grid'], null) : null;
    $enabled = isset($_POST['enabled']) ? validate_bool($_POST['enabled']) : null;
    
    $result = update_pattern($id, $name, $description, $grid, $enabled);
    echo json_encode($result);
    exit;
}

// Invalid request
echo json_encode(['success' => false, 'error' => 'Invalid request']);
exit;

?>
