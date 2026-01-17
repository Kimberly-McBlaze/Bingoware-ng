<?php
/**
 * Themes API Endpoint
 * Handles JSON requests for theme CRUD/list/import/export operations
 * 
 * Methods:
 * - GET  /api/themes.php              - List all themes
 * - GET  /api/themes.php?id=XXX       - Get single theme
 * - GET  /api/themes.php?export=1     - Export all themes as JSON
 * - POST /api/themes.php              - Create/Update theme
 * - POST /api/themes.php (delete_id)  - Delete theme
 * - POST /api/themes.php (import)     - Import themes from JSON
 * - POST /api/themes.php (activate)   - Activate a theme
 */

// Load bootstrap
include_once(__DIR__ . "/../include/bootstrap.php");
include_once(__DIR__ . "/../include/storage.php");

// Set JSON content type
header('Content-Type: application/json');

// Get HTTP method
$method = $_SERVER['REQUEST_METHOD'];

/**
 * Get themes file path
 */
function get_themes_file() {
    return __DIR__ . "/../data/themes.json";
}

/**
 * Load all themes
 */
function load_themes() {
    $file = get_themes_file();
    
    if (!file_exists($file)) {
        // Return default themes if file doesn't exist
        return get_default_themes();
    }
    
    $data = read_json($file, null);
    if ($data === null) {
        return get_default_themes();
    }
    
    return $data['themes'] ?? get_default_themes();
}

/**
 * Save themes
 */
function save_themes($themes) {
    $file = get_themes_file();
    
    // Ensure data directory exists
    $data_dir = dirname($file);
    if (!ensure_data_dir($data_dir)) {
        return false;
    }
    
    $data = [
        'version' => 1,
        'themes' => $themes
    ];
    
    return atomic_write_json($file, $data);
}

/**
 * Get default themes
 */
function get_default_themes() {
    return [
        [
            'id' => 'theme_default_light',
            'name' => 'Light Modern',
            'description' => 'Clean and bright modern theme',
            'is_default' => true,
            'is_active' => true,
            'colors' => [
                'primary' => '#667eea',
                'secondary' => '#764ba2',
                'success' => '#10b981',
                'warning' => '#f59e0b',
                'error' => '#ef4444',
                'bg-primary' => '#ffffff',
                'bg-secondary' => '#f3f4f6',
                'bg-tertiary' => '#e5e7eb',
                'text-primary' => '#1f2937',
                'text-secondary' => '#6b7280',
                'text-muted' => '#9ca3af',
                'border-color' => '#d1d5db'
            ]
        ]
    ];
}

/**
 * Get a single theme by ID
 */
function get_theme_by_id($theme_id) {
    $themes = load_themes();
    
    foreach ($themes as $theme) {
        if ($theme['id'] === $theme_id) {
            return $theme;
        }
    }
    
    return null;
}

/**
 * Get active theme
 */
function get_active_theme() {
    $themes = load_themes();
    
    foreach ($themes as $theme) {
        if (isset($theme['is_active']) && $theme['is_active']) {
            return $theme;
        }
    }
    
    // Return first theme if none is active
    return count($themes) > 0 ? $themes[0] : null;
}

/**
 * Activate a theme
 */
function activate_theme($theme_id) {
    $themes = load_themes();
    $found = false;
    
    // Deactivate all themes and activate the selected one
    foreach ($themes as $idx => $theme) {
        if ($theme['id'] === $theme_id) {
            $themes[$idx]['is_active'] = true;
            $found = true;
        } else {
            $themes[$idx]['is_active'] = false;
        }
    }
    
    if (!$found) {
        return ['success' => false, 'error' => 'Theme not found'];
    }
    
    if (save_themes($themes)) {
        return ['success' => true, 'theme' => get_theme_by_id($theme_id)];
    } else {
        return ['success' => false, 'error' => 'Failed to save themes'];
    }
}

/**
 * Create a new theme
 */
function create_theme($name, $description, $colors) {
    $themes = load_themes();
    
    // Validate unique name
    foreach ($themes as $theme) {
        if (strcasecmp($theme['name'], $name) === 0) {
            return ['success' => false, 'error' => 'A theme with this name already exists'];
        }
    }
    
    // Validate colors
    $validation = validate_theme_colors($colors);
    if (!$validation['valid']) {
        return ['success' => false, 'error' => $validation['error']];
    }
    
    // Generate ID
    $new_id = 'theme_' . uniqid();
    
    $new_theme = [
        'id' => $new_id,
        'name' => trim($name),
        'description' => trim($description),
        'is_default' => false,
        'is_active' => false,
        'colors' => $colors
    ];
    
    $themes[] = $new_theme;
    
    if (save_themes($themes)) {
        return ['success' => true, 'theme' => $new_theme];
    } else {
        return ['success' => false, 'error' => 'Failed to save theme'];
    }
}

/**
 * Update an existing theme
 */
function update_theme($theme_id, $name, $description, $colors) {
    $themes = load_themes();
    $found = false;
    
    foreach ($themes as $idx => $theme) {
        if ($theme['id'] === $theme_id) {
            $found = true;
            
            // Don't allow editing default themes' structure, only activation
            if ($theme['is_default']) {
                return ['success' => false, 'error' => 'Cannot edit default themes'];
            }
            
            // Check for duplicate name
            if ($name !== null && $name !== '' && strcasecmp($theme['name'], $name) !== 0) {
                foreach ($themes as $other_theme) {
                    if ($other_theme['id'] !== $theme_id && strcasecmp($other_theme['name'], $name) === 0) {
                        return ['success' => false, 'error' => 'A theme with this name already exists'];
                    }
                }
            }
            
            // Validate colors
            if ($colors !== null) {
                $validation = validate_theme_colors($colors);
                if (!$validation['valid']) {
                    return ['success' => false, 'error' => $validation['error']];
                }
                $themes[$idx]['colors'] = $colors;
            }
            
            // Update name and description if provided
            if ($name !== null && $name !== '') {
                $themes[$idx]['name'] = trim($name);
            }
            if ($description !== null) {
                $themes[$idx]['description'] = trim($description);
            }
            
            break;
        }
    }
    
    if (!$found) {
        return ['success' => false, 'error' => 'Theme not found'];
    }
    
    if (save_themes($themes)) {
        return ['success' => true];
    } else {
        return ['success' => false, 'error' => 'Failed to save theme'];
    }
}

/**
 * Delete a theme
 */
function delete_theme($theme_id) {
    $themes = load_themes();
    $new_themes = [];
    $found = false;
    
    foreach ($themes as $theme) {
        if ($theme['id'] === $theme_id) {
            $found = true;
            
            // Don't allow deleting default themes
            if ($theme['is_default']) {
                return ['success' => false, 'error' => 'Cannot delete default themes'];
            }
            
            // Don't allow deleting active theme
            if (isset($theme['is_active']) && $theme['is_active']) {
                return ['success' => false, 'error' => 'Cannot delete the active theme. Please activate another theme first.'];
            }
        } else {
            $new_themes[] = $theme;
        }
    }
    
    if (!$found) {
        return ['success' => false, 'error' => 'Theme not found'];
    }
    
    if (save_themes($new_themes)) {
        return ['success' => true];
    } else {
        return ['success' => false, 'error' => 'Failed to save themes'];
    }
}

/**
 * Validate theme colors
 */
function validate_theme_colors($colors) {
    if (!is_array($colors)) {
        return ['valid' => false, 'error' => 'Colors must be an object'];
    }
    
    $required_keys = ['primary', 'secondary', 'success', 'warning', 'error', 
                      'bg-primary', 'bg-secondary', 'bg-tertiary', 
                      'text-primary', 'text-secondary', 'text-muted', 'border-color'];
    
    foreach ($required_keys as $key) {
        if (!isset($colors[$key])) {
            return ['valid' => false, 'error' => "Missing required color: $key"];
        }
        
        // Validate hex color format
        if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $colors[$key])) {
            return ['valid' => false, 'error' => "Invalid color format for $key (must be #RRGGBB)"];
        }
    }
    
    return ['valid' => true];
}

/**
 * Import themes from JSON
 */
function import_themes($json_data) {
    try {
        $data = json_decode($json_data, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['success' => false, 'error' => 'Invalid JSON: ' . json_last_error_msg()];
        }
        
        if (!isset($data['themes']) || !is_array($data['themes'])) {
            return ['success' => false, 'error' => 'Invalid theme file format'];
        }
        
        $current_themes = load_themes();
        $imported_count = 0;
        
        foreach ($data['themes'] as $import_theme) {
            // Skip if required fields are missing
            if (!isset($import_theme['name']) || !isset($import_theme['colors'])) {
                continue;
            }
            
            // Validate colors
            $validation = validate_theme_colors($import_theme['colors']);
            if (!$validation['valid']) {
                continue; // Skip invalid themes
            }
            
            // Check for name conflicts
            $name_exists = false;
            foreach ($current_themes as $existing_theme) {
                if (strcasecmp($existing_theme['name'], $import_theme['name']) === 0) {
                    $name_exists = true;
                    break;
                }
            }
            
            if (!$name_exists) {
                // Add theme with new ID
                $current_themes[] = [
                    'id' => 'theme_' . uniqid(),
                    'name' => $import_theme['name'],
                    'description' => isset($import_theme['description']) ? $import_theme['description'] : '',
                    'is_default' => false,
                    'is_active' => false,
                    'colors' => $import_theme['colors']
                ];
                $imported_count++;
            }
        }
        
        if ($imported_count > 0) {
            if (save_themes($current_themes)) {
                return ['success' => true, 'message' => "Successfully imported $imported_count theme(s)"];
            } else {
                return ['success' => false, 'error' => 'Failed to save imported themes'];
            }
        } else {
            return ['success' => false, 'error' => 'No valid themes found to import'];
        }
        
    } catch (Exception $e) {
        return ['success' => false, 'error' => 'Import failed: ' . $e->getMessage()];
    }
}

// === REQUEST HANDLERS ===

// Export themes
if ($method === 'GET' && isset($_GET['export'])) {
    $themes = load_themes();
    $export_data = [
        'version' => 1,
        'themes' => $themes
    ];
    
    header('Content-Disposition: attachment; filename="bingoware-themes-' . date('Y-m-d') . '.json"');
    echo json_encode($export_data, JSON_PRETTY_PRINT);
    exit;
}

// List all themes
if ($method === 'GET' && !isset($_GET['id'])) {
    echo json_encode(['success' => true, 'themes' => load_themes()]);
    exit;
}

// Get single theme
if ($method === 'GET' && isset($_GET['id'])) {
    $theme_id = validate_string($_GET['id'], 50);
    if (!$theme_id) {
        echo json_encode(['success' => false, 'error' => 'Invalid theme ID']);
        exit;
    }
    
    $theme = get_theme_by_id($theme_id);
    if ($theme) {
        echo json_encode(['success' => true, 'theme' => $theme]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Theme not found']);
    }
    exit;
}

// Get active theme
if ($method === 'GET' && isset($_GET['active'])) {
    $theme = get_active_theme();
    if ($theme) {
        echo json_encode(['success' => true, 'theme' => $theme]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No active theme found']);
    }
    exit;
}

// Activate theme
if ($method === 'POST' && isset($_POST['activate'])) {
    $theme_id = validate_string($_POST['activate'], 50);
    if (!$theme_id) {
        echo json_encode(['success' => false, 'error' => 'Invalid theme ID']);
        exit;
    }
    
    $result = activate_theme($theme_id);
    echo json_encode($result);
    exit;
}

// Delete theme
if ($method === 'POST' && isset($_POST['delete_id'])) {
    $theme_id = validate_string($_POST['delete_id'], 50);
    if (!$theme_id) {
        echo json_encode(['success' => false, 'error' => 'Invalid theme ID']);
        exit;
    }
    
    $result = delete_theme($theme_id);
    echo json_encode($result);
    exit;
}

// Import themes
if ($method === 'POST' && isset($_FILES['import_file'])) {
    if ($_FILES['import_file']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'error' => 'File upload failed']);
        exit;
    }
    
    $json_data = file_get_contents($_FILES['import_file']['tmp_name']);
    $result = import_themes($json_data);
    echo json_encode($result);
    exit;
}

// Create theme
if ($method === 'POST' && (!isset($_POST['id']) || empty($_POST['id']))) {
    $name = validate_string($_POST['name'] ?? '', 50);
    $description = validate_string($_POST['description'] ?? '', 200);
    $colors = validate_json($_POST['colors'] ?? '{}', []);
    
    if (empty($name)) {
        echo json_encode(['success' => false, 'error' => 'Theme name is required']);
        exit;
    }
    
    $result = create_theme($name, $description, $colors);
    echo json_encode($result);
    exit;
}

// Update theme
if ($method === 'POST' && isset($_POST['id']) && !empty($_POST['id'])) {
    $id = validate_string($_POST['id'], 50);
    if (!$id) {
        echo json_encode(['success' => false, 'error' => 'Invalid theme ID']);
        exit;
    }
    
    $name = validate_string($_POST['name'] ?? '', 50);
    $description = validate_string($_POST['description'] ?? '', 200);
    $colors = isset($_POST['colors']) ? validate_json($_POST['colors'], null) : null;
    
    $result = update_theme($id, $name, $description, $colors);
    echo json_encode($result);
    exit;
}

// Invalid request
echo json_encode(['success' => false, 'error' => 'Invalid request']);
exit;

?>
