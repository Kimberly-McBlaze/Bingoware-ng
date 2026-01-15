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
 * 
 * @return array Stack ID => stack data mappings
 */
function load_virtual_card_stacks() {
    global $setid;
    
    // Validate setid to prevent path traversal
    if (!preg_match('/^[a-zA-Z0-9_-]+$/', $setid)) {
        error_log("Invalid setid for virtual card stacks: $setid");
        return [];
    }
    
    // Ensure data directory exists
    if (!file_exists("data")) {
        mkdir("data", 0755, true);
    }
    
    $filepath = __DIR__ . "/../data/virtualstacks." . $setid . ".dat";
    
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
 * 
 * @param array $stacks Stack ID => stack data mappings
 * @return bool Success status
 */
function save_virtual_card_stacks($stacks) {
    global $setid;
    
    // Validate setid to prevent path traversal
    if (!preg_match('/^[a-zA-Z0-9_-]+$/', $setid)) {
        error_log("Invalid setid for virtual card stacks: $setid");
        return false;
    }
    
    // Ensure data directory exists
    if (!file_exists("data")) {
        if (!mkdir("data", 0755, true)) {
            error_log("Failed to create data directory");
            return false;
        }
    }
    
    $filepath = __DIR__ . "/../data/virtualstacks." . $setid . ".dat";
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
 * Get all virtual card stacks for the current set for display purposes
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
        if ($data['setid'] === $setid) {
            $card_ids = [];
            foreach ($data['card_numbers'] as $card_number) {
                $card_ids[] = sprintf("%s%'04d", $setid, $card_number + 1);
            }
            
            $display_stacks[] = [
                'stack_id' => $stack_id,
                'card_ids' => $card_ids,
                'count' => count($data['card_numbers']),
                'url' => $base_url . 'virtual_stack.php?stack=' . $stack_id,
                'created' => $data['created'],
            ];
        }
    }
    
    // Sort by creation time (newest first)
    usort($display_stacks, function($a, $b) {
        return $b['created'] - $a['created'];
    });
    
    return $display_stacks;
}

?>

/**
 * Delete all virtual card stacks for the current set
 * 
 * @return bool Success status
 */
function delete_all_virtual_stacks() {
    global $setid;
    
    // Validate setid to prevent path traversal
    if (!preg_match('/^[a-zA-Z0-9_-]+$/', $setid)) {
        error_log("Invalid setid for deleting virtual card stacks: $setid");
        return false;
    }
    
    $filepath = __DIR__ . "/../data/virtualstacks." . $setid . ".dat";
    
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
 * Check if there are any virtual card stacks for the current set
 * 
 * @return bool True if stacks exist, false otherwise
 */
function has_virtual_stacks() {
    $stacks = load_virtual_card_stacks();
    return !empty($stacks);
}
