<?php
/**
 * Virtual Bingo Cards Helper Functions
 * Manages token generation and card allocation for virtual bingo
 */

/**
 * Generate a stack ID for grouping multiple cards
 * 
 * @return string 32-character hexadecimal stack ID
 */
function generate_stack_id() {
    return bin2hex(random_bytes(16));
}

/**
 * Generate virtual cards as a stack and return shareable URL
 * 
 * @param int $count Number of cards to generate in this stack
 * @return array Result with 'success', 'stack_id', 'stack_url', 'card_ids' or 'error'
 */
function generate_virtual_cards($count) {
    global $setid;
    
    // Ensure card set exists
    if (!set_exists()) {
        return ['success' => false, 'error' => 'No card set found. Please generate cards first.'];
    }
    
    $total_cards = card_number();
    if ($total_cards < 1) {
        return ['success' => false, 'error' => 'Card set is empty.'];
    }
    
    // Generate unique stack ID
    $stack_id = generate_stack_id();
    
    // Load existing stacks
    $stacks = load_virtual_card_stacks();
    
    // Ensure stack ID is unique
    while (isset($stacks[$stack_id])) {
        $stack_id = generate_stack_id();
    }
    
    // Determine base URL
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $script_dir = dirname($_SERVER['SCRIPT_NAME']);
    $base_url = $protocol . '://' . $host . $script_dir;
    if (substr($base_url, -1) !== '/') {
        $base_url .= '/';
    }
    
    $allocated_cards = get_allocated_card_numbers_from_stacks($stacks);
    $card_numbers = [];
    $card_ids = [];
    
    for ($i = 0; $i < $count; $i++) {
        // Find an available card number (0-indexed)
        $card_number = find_available_card($total_cards, $allocated_cards);
        if ($card_number === null) {
            // All cards allocated, reuse from the set
            $card_number = rand(0, $total_cards - 1);
        } else {
            $allocated_cards[] = $card_number;
        }
        
        $card_numbers[] = $card_number;
        $card_id = sprintf("%s%'04d", $setid, $card_number + 1);
        $card_ids[] = $card_id;
    }
    
    // Create stack entry
    $stacks[$stack_id] = [
        'setid' => $setid,
        'card_numbers' => $card_numbers,
        'created' => time(),
    ];
    
    // Save stacks
    if (!save_virtual_card_stacks($stacks)) {
        return ['success' => false, 'error' => 'Failed to save card stack.'];
    }
    
    return [
        'success' => true, 
        'stack_id' => $stack_id,
        'stack_url' => $base_url . 'virtual_stack.php?stack=' . $stack_id,
        'card_ids' => $card_ids,
        'count' => $count,
    ];
}

/**
 * Find an available card number that hasn't been allocated yet
 * 
 * @param int $total_cards Total number of cards in set
 * @param array $allocated List of already allocated card numbers
 * @return int|null Available card number or null if all allocated
 */
function find_available_card($total_cards, $allocated) {
    for ($i = 0; $i < $total_cards; $i++) {
        if (!in_array($i, $allocated)) {
            return $i;
        }
    }
    return null;
}

/**
 * Get list of all allocated card numbers from stacks
 * 
 * @param array $stacks Virtual card stacks
 * @return array List of allocated card numbers
 */
function get_allocated_card_numbers_from_stacks($stacks) {
    global $setid;
    $allocated = [];
    
    foreach ($stacks as $stack_id => $data) {
        if ($data['setid'] === $setid) {
            $allocated = array_merge($allocated, $data['card_numbers']);
        }
    }
    
    return $allocated;
}

/**
 * Load virtual card stacks from storage
 * Multi-set support: stacks are now stored globally, not per-set
 * 
 * @return array Stack ID => stack data mappings
 */
function load_virtual_card_stacks() {
    // Ensure data directory exists
    if (!file_exists("data")) {
        mkdir("data", 0755, true);
    }
    
    // Use global storage file for all stacks
    $filepath = __DIR__ . "/../data/virtualstacks.dat";
    
    if (!file_exists($filepath)) {
        return [];
    }
    
    $contents = file_get_contents($filepath);
    if ($contents === false) {
        error_log("Failed to read virtual card stacks: $filepath");
        return [];
    }
    
    $data = unserialize($contents, ['allowed_classes' => false]);
    if ($data === false) {
        error_log("Failed to unserialize virtual card stacks: $filepath");
        return [];
    }
    
    return is_array($data) ? $data : [];
}

/**
 * Save virtual card stacks to storage
 * Multi-set support: stacks are now stored globally
 * 
 * @param array $stacks Stack ID => stack data mappings
 * @return bool Success status
 */
function save_virtual_card_stacks($stacks) {
    // Ensure data directory exists
    if (!file_exists("data")) {
        if (!mkdir("data", 0755, true)) {
            error_log("Failed to create data directory");
            return false;
        }
    }
    
    // Use global storage file
    $filepath = __DIR__ . "/../data/virtualstacks.dat";
    $serialized = serialize($stacks);
    
    $result = file_put_contents($filepath, $serialized);
    if ($result === false) {
        error_log("Failed to save virtual card stacks: $filepath");
        return false;
    }
    
    return true;
}

/**
 * Get stack data from stack ID
 * 
 * @param string $stack_id Stack identifier
 * @return array|null Stack data or null if not found
 */
function get_stack_from_id($stack_id) {
    // Validate stack ID format
    if (!preg_match('/^[a-f0-9]{32}$/', $stack_id)) {
        return null;
    }
    
    $stacks = load_virtual_card_stacks();
    
    if (!isset($stacks[$stack_id])) {
        return null;
    }
    
    return $stacks[$stack_id];
}

/**
 * Get all virtual card stacks for display purposes
 * Multi-set support: shows all stacks, highlighting which ones belong to current set
 * 
 * @return array List of stacks with their URLs
 */
function get_all_virtual_stacks_for_display() {
    global $setid;
    
    $stacks = load_virtual_card_stacks();
    $display_stacks = [];
    
    // Determine base URL
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $script_dir = dirname($_SERVER['SCRIPT_NAME']);
    $base_url = $protocol . '://' . $host . $script_dir;
    if (substr($base_url, -1) !== '/') {
        $base_url .= '/';
    }
    
    foreach ($stacks as $stack_id => $data) {
        $stack_setid = $data['setid'];
        $is_current_set = ($stack_setid === $setid);
        
        $card_ids = [];
        foreach ($data['card_numbers'] as $card_number) {
            $card_ids[] = sprintf("%s%'04d", $stack_setid, $card_number + 1);
        }
        
        $display_stacks[] = [
            'stack_id' => $stack_id,
            'setid' => $stack_setid,
            'card_ids' => $card_ids,
            'count' => count($data['card_numbers']),
            'url' => $base_url . 'virtual_stack.php?stack=' . $stack_id,
            'created' => $data['created'],
            'is_current_set' => $is_current_set,
        ];
    }
    
    // Sort by creation time (newest first)
    usort($display_stacks, function($a, $b) {
        return $b['created'] - $a['created'];
    });
    
    return $display_stacks;
}

/**
 * Delete all virtual card stacks
 * Multi-set support: deletes all stacks globally
 * 
 * @return bool Success status
 */
function delete_all_virtual_stacks() {
    $filepath = __DIR__ . "/../data/virtualstacks.dat";
    
    if (file_exists($filepath)) {
        $result = unlink($filepath);
        if (!$result) {
            error_log("Failed to delete virtual card stacks file: $filepath");
            return false;
        }
    }
    
    return true;
}

/**
 * Check if there are any virtual card stacks
 * 
 * @return bool True if stacks exist, false otherwise
 */
function has_virtual_stacks() {
    $stacks = load_virtual_card_stacks();
    return !empty($stacks);
}

/**
 * Delete a specific virtual card stack by its ID
 * 
 * @param string $stack_id Stack identifier to delete
 * @return array Result with 'success' and optional 'error' message
 */
function delete_virtual_stack($stack_id) {
    // Validate stack ID format
    if (!preg_match('/^[a-f0-9]{32}$/', $stack_id)) {
        return ['success' => false, 'error' => 'Invalid stack ID format'];
    }
    
    // Load all stacks
    $stacks = load_virtual_card_stacks();
    
    // Check if stack exists
    if (!isset($stacks[$stack_id])) {
        return ['success' => false, 'error' => 'Stack not found'];
    }
    
    // Remove the stack
    unset($stacks[$stack_id]);
    
    // Save updated stacks
    if (!save_virtual_card_stacks($stacks)) {
        return ['success' => false, 'error' => 'Failed to save changes'];
    }
    
    return ['success' => true];
}

/**
 * Get all virtual card numbers (0-indexed) for the current set
 * Used to filter out virtual-assigned cards from print output
 * 
 * @return array List of card numbers (0-indexed) that are assigned to virtual stacks
 */
function get_all_virtual_card_numbers() {
    $stacks = load_virtual_card_stacks();
    return get_allocated_card_numbers_from_stacks($stacks);
}

/**
 * Migrate old per-set virtual stack files to new global format
 * This ensures backward compatibility with existing stacks
 * 
 * @return bool True if migration was performed, false if not needed or failed
 */
function migrate_virtual_stacks_to_global() {
    $data_dir = __DIR__ . "/../data";
    $global_file = $data_dir . "/virtualstacks.dat";
    
    // If global file already exists, no migration needed
    if (file_exists($global_file)) {
        return false;
    }
    
    // Find all old per-set stack files
    $old_files = glob($data_dir . "/virtualstacks.*.dat");
    if (empty($old_files)) {
        return false;
    }
    
    $merged_stacks = [];
    
    // Merge all old files into one
    foreach ($old_files as $old_file) {
        $contents = file_get_contents($old_file);
        if ($contents === false) {
            error_log("Failed to read old stack file during migration: $old_file");
            continue;
        }
        
        $stacks = unserialize($contents, ['allowed_classes' => false]);
        if ($stacks === false || !is_array($stacks)) {
            error_log("Failed to unserialize old stack file during migration: $old_file");
            continue;
        }
        
        // Merge into global array
        $merged_stacks = array_merge($merged_stacks, $stacks);
    }
    
    // Save merged stacks to global file
    if (!empty($merged_stacks)) {
        $serialized = serialize($merged_stacks);
        $result = file_put_contents($global_file, $serialized);
        
        if ($result !== false) {
            // Migration successful, clean up old files
            foreach ($old_files as $old_file) {
                if (!unlink($old_file)) {
                    error_log("Failed to delete old virtual stack file during migration: $old_file");
                }
            }
            error_log("Migrated " . count($merged_stacks) . " virtual stacks from " . count($old_files) . " old files to new global format");
            return true;
        } else {
            error_log("Failed to save merged stacks during migration");
            return false;
        }
    }
    
    return false;
}
?>
