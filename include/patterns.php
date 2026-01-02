<?php
/**
 * Winning Patterns Management Functions
 * Handles CRUD operations for winning patterns
 */

/**
 * Get the patterns storage file path
 */
function get_patterns_file() {
    return "data/patterns.json";
}

/**
 * Load all patterns from JSON storage
 * Returns array of patterns with metadata
 */
function load_patterns() {
    $file = get_patterns_file();
    
    if (!file_exists($file)) {
        // First time - migrate from old format
        migrate_patterns_from_old_format();
    }
    
    if (file_exists($file)) {
        $json = file_get_contents($file);
        $data = json_decode($json, true);
        if ($data === null) {
            error_log("Failed to parse patterns.json");
            return get_default_patterns();
        }
        return $data['patterns'] ?? get_default_patterns();
    }
    
    return get_default_patterns();
}

/**
 * Save patterns to JSON storage
 */
function save_patterns($patterns) {
    $file = get_patterns_file();
    
    // Ensure data directory exists
    if (!file_exists("data")) {
        @mkdir("data", 0755, true);
    }
    
    $data = [
        'version' => 1,
        'patterns' => $patterns
    ];
    
    $json = json_encode($data, JSON_PRETTY_PRINT);
    return @file_put_contents($file, $json) !== false;
}

/**
 * Get default patterns with metadata
 */
function get_default_patterns() {
    global $patternkeywords;
    
    $defaults = [
        [
            'id' => 'pattern_0',
            'name' => 'Normal',
            'description' => 'Any row, column, or diagonal',
            'enabled' => true,
            'is_default' => true,
            'is_special' => true, // Pattern 0 is handled specially in code
            'grid' => null // No grid needed for pattern 0
        ]
    ];
    
    // Add the rest from old format
    $oldPatternNames = [
        'Four Corners', 'Cross-Shaped', 'T-Shaped', 'X-Shaped', 
        '+ Shaped', 'Z-Shaped', 'N-Shaped', 'Box Shaped', 
        'Square Shaped', 'Blackout (Full Card)'
    ];
    
    for ($i = 0; $i < count($oldPatternNames); $i++) {
        $defaults[] = [
            'id' => 'pattern_' . ($i + 1),
            'name' => $oldPatternNames[$i],
            'description' => '',
            'enabled' => false,
            'is_default' => true,
            'is_special' => false,
            'grid' => null // Will be loaded from winningpatterns.dat
        ];
    }
    
    return $defaults;
}

/**
 * Migrate patterns from old format (winningpatterns.dat + settings.php)
 */
function migrate_patterns_from_old_format() {
    global $winningpatternarray, $patternkeywords;
    
    $patterns = [];
    
    // Pattern 0 (Normal) - special case
    $patterns[] = [
        'id' => 'pattern_0',
        'name' => 'Normal',
        'description' => 'Any row, column, or diagonal',
        'enabled' => isset($winningpatternarray[0]) && $winningpatternarray[0] == 'on',
        'is_default' => true,
        'is_special' => true,
        'grid' => null
    ];
    
    // Load grid patterns from winningpatterns.dat
    $winningset = load_winning_patterns();
    
    if (is_array($winningset)) {
        for ($i = 1; $i < count($patternkeywords); $i++) {
            $grid = [];
            
            // Convert old format to simple grid array
            if (isset($winningset[$i - 1])) {
                for ($col = 0; $col < 5; $col++) {
                    for ($row = 0; $row < 5; $row++) {
                        if ($winningset[$i - 1][$col][$row]["checked"]) {
                            $grid[] = ['col' => $col, 'row' => $row];
                        }
                    }
                }
            }
            
            $patterns[] = [
                'id' => 'pattern_' . $i,
                'name' => $patternkeywords[$i],
                'description' => '',
                'enabled' => isset($winningpatternarray[$i]) && $winningpatternarray[$i] == 'on',
                'is_default' => true,
                'is_special' => false,
                'grid' => $grid
            ];
        }
    }
    
    save_patterns($patterns);
    return $patterns;
}

/**
 * Get a single pattern by ID
 */
function get_pattern_by_id($pattern_id) {
    $patterns = load_patterns();
    
    foreach ($patterns as $pattern) {
        if ($pattern['id'] === $pattern_id) {
            return $pattern;
        }
    }
    
    return null;
}

/**
 * Create a new pattern
 */
function create_pattern($name, $description, $grid, $enabled = false) {
    $patterns = load_patterns();
    
    // Validate unique name
    foreach ($patterns as $pattern) {
        if (strcasecmp($pattern['name'], $name) === 0) {
            return ['success' => false, 'error' => 'A pattern with this name already exists'];
        }
    }
    
    // Validate grid
    $validation = validate_pattern_grid($grid);
    if (!$validation['valid']) {
        return ['success' => false, 'error' => $validation['error']];
    }
    
    // Generate ID
    $new_id = 'pattern_' . uniqid();
    
    $new_pattern = [
        'id' => $new_id,
        'name' => trim($name),
        'description' => trim($description),
        'enabled' => $enabled,
        'is_default' => false,
        'is_special' => false,
        'grid' => $grid
    ];
    
    $patterns[] = $new_pattern;
    
    if (save_patterns($patterns)) {
        return ['success' => true, 'pattern' => $new_pattern];
    } else {
        return ['success' => false, 'error' => 'Failed to save pattern'];
    }
}

/**
 * Update an existing pattern
 */
function update_pattern($pattern_id, $name, $description, $grid, $enabled = null) {
    $patterns = load_patterns();
    $found = false;
    
    foreach ($patterns as $idx => $pattern) {
        // Check for duplicate name (excluding current pattern)
        if ($pattern['id'] !== $pattern_id && strcasecmp($pattern['name'], $name) === 0) {
            return ['success' => false, 'error' => 'A pattern with this name already exists'];
        }
        
        if ($pattern['id'] === $pattern_id) {
            $found = true;
            
            // Don't allow editing special patterns (pattern 0)
            if ($pattern['is_special']) {
                // Only allow toggling enabled state
                if ($enabled !== null) {
                    $patterns[$idx]['enabled'] = $enabled;
                }
            } else {
                // Validate grid for non-special patterns
                if ($grid !== null) {
                    $validation = validate_pattern_grid($grid);
                    if (!$validation['valid']) {
                        return ['success' => false, 'error' => $validation['error']];
                    }
                    $patterns[$idx]['grid'] = $grid;
                }
                
                $patterns[$idx]['name'] = trim($name);
                $patterns[$idx]['description'] = trim($description);
                if ($enabled !== null) {
                    $patterns[$idx]['enabled'] = $enabled;
                }
            }
        }
    }
    
    if (!$found) {
        return ['success' => false, 'error' => 'Pattern not found'];
    }
    
    if (save_patterns($patterns)) {
        return ['success' => true];
    } else {
        return ['success' => false, 'error' => 'Failed to save pattern'];
    }
}

/**
 * Delete a pattern
 */
function delete_pattern($pattern_id) {
    $patterns = load_patterns();
    $new_patterns = [];
    $found = false;
    
    foreach ($patterns as $pattern) {
        if ($pattern['id'] === $pattern_id) {
            $found = true;
            
            // Don't allow deleting default patterns
            if ($pattern['is_default']) {
                return ['success' => false, 'error' => 'Cannot delete default patterns'];
            }
        } else {
            $new_patterns[] = $pattern;
        }
    }
    
    if (!$found) {
        return ['success' => false, 'error' => 'Pattern not found'];
    }
    
    if (save_patterns($new_patterns)) {
        return ['success' => true];
    } else {
        return ['success' => false, 'error' => 'Failed to save patterns'];
    }
}

/**
 * Validate a pattern grid
 */
function validate_pattern_grid($grid) {
    if (!is_array($grid)) {
        return ['valid' => false, 'error' => 'Grid must be an array'];
    }
    
    if (count($grid) < 1) {
        return ['valid' => false, 'error' => 'Pattern must have at least one square selected'];
    }
    
    if (count($grid) > 25) {
        return ['valid' => false, 'error' => 'Pattern cannot have more than 25 squares'];
    }
    
    // Validate each square
    foreach ($grid as $square) {
        if (!isset($square['col']) || !isset($square['row'])) {
            return ['valid' => false, 'error' => 'Each square must have col and row'];
        }
        
        if ($square['col'] < 0 || $square['col'] > 4 || $square['row'] < 0 || $square['row'] > 4) {
            return ['valid' => false, 'error' => 'Grid coordinates must be between 0 and 4'];
        }
    }
    
    return ['valid' => true];
}

/**
 * Get enabled patterns for game play
 */
function get_enabled_patterns() {
    $patterns = load_patterns();
    return array_filter($patterns, function($p) {
        return $p['enabled'] === true;
    });
}

/**
 * Convert grid to old winningset format for backward compatibility
 */
function grid_to_winningset_format($grid) {
    // Initialize empty 5x5 grid
    $winningset = [];
    for ($col = 0; $col < 5; $col++) {
        for ($row = 0; $row < 5; $row++) {
            $winningset[$col][$row] = [
                'number' => ($col * 15) + $row + 1, // Dummy numbers
                'checked' => false
            ];
        }
    }
    
    // Mark checked squares
    if (is_array($grid)) {
        foreach ($grid as $square) {
            $winningset[$square['col']][$square['row']]['checked'] = true;
        }
    }
    
    return $winningset;
}

?>
