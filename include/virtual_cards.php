<?php
/**
 * Virtual Bingo Cards Helper Functions
 * Manages token generation and card allocation for virtual bingo
 */

/**
 * Generate a secure random token for card access
 * 
 * @return string 32-character hexadecimal token
 */
function generate_card_token() {
    return bin2hex(random_bytes(16));
}

/**
 * Generate virtual cards and return shareable links
 * 
 * @param int $count Number of cards to generate
 * @return array Result with 'success', 'cards' or 'error'
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
    
    // Load existing virtual card mappings
    $mappings = load_virtual_card_mappings();
    
    // Determine base URL
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $script_dir = dirname($_SERVER['SCRIPT_NAME']);
    $base_url = $protocol . '://' . $host . $script_dir;
    if (substr($base_url, -1) !== '/') {
        $base_url .= '/';
    }
    
    $cards = [];
    $allocated_cards = get_allocated_card_numbers($mappings);
    
    for ($i = 0; $i < $count; $i++) {
        // Find an available card number (0-indexed)
        $card_number = find_available_card($total_cards, $allocated_cards);
        if ($card_number === null) {
            // All cards allocated, reuse from the set
            $card_number = rand(0, $total_cards - 1);
        } else {
            $allocated_cards[] = $card_number;
        }
        
        // Generate unique token
        $token = generate_card_token();
        while (isset($mappings[$token])) {
            $token = generate_card_token(); // Regenerate if collision (very unlikely)
        }
        
        // Create mapping
        $mappings[$token] = [
            'setid' => $setid,
            'card_number' => $card_number,
            'created' => time(),
        ];
        
        // Format card ID for display
        $card_id = sprintf("%s%'04d", $setid, $card_number + 1);
        
        $cards[] = [
            'token' => $token,
            'card_id' => $card_id,
            'url' => $base_url . 'virtual_card.php?token=' . $token,
        ];
    }
    
    // Save mappings
    if (!save_virtual_card_mappings($mappings)) {
        return ['success' => false, 'error' => 'Failed to save card mappings.'];
    }
    
    return ['success' => true, 'cards' => $cards];
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
 * Get list of all allocated card numbers from mappings
 * 
 * @param array $mappings Virtual card mappings
 * @return array List of allocated card numbers
 */
function get_allocated_card_numbers($mappings) {
    global $setid;
    $allocated = [];
    
    foreach ($mappings as $token => $data) {
        if ($data['setid'] === $setid) {
            $allocated[] = $data['card_number'];
        }
    }
    
    return $allocated;
}

/**
 * Load virtual card mappings from storage
 * 
 * @return array Token => card data mappings
 */
function load_virtual_card_mappings() {
    global $setid;
    
    // Validate setid to prevent path traversal
    if (!preg_match('/^[a-zA-Z0-9_-]+$/', $setid)) {
        error_log("Invalid setid for virtual card mappings: $setid");
        return [];
    }
    
    // Ensure data directory exists
    if (!file_exists("data")) {
        mkdir("data", 0755, true);
    }
    
    $filepath = __DIR__ . "/../data/virtualcards." . $setid . ".dat";
    
    if (!file_exists($filepath)) {
        return [];
    }
    
    $contents = file_get_contents($filepath);
    if ($contents === false) {
        error_log("Failed to read virtual card mappings: $filepath");
        return [];
    }
    
    $data = unserialize($contents, ['allowed_classes' => false]);
    if ($data === false) {
        error_log("Failed to unserialize virtual card mappings: $filepath");
        return [];
    }
    
    return is_array($data) ? $data : [];
}

/**
 * Save virtual card mappings to storage
 * 
 * @param array $mappings Token => card data mappings
 * @return bool Success status
 */
function save_virtual_card_mappings($mappings) {
    global $setid;
    
    // Validate setid to prevent path traversal
    if (!preg_match('/^[a-zA-Z0-9_-]+$/', $setid)) {
        error_log("Invalid setid for virtual card mappings: $setid");
        return false;
    }
    
    // Ensure data directory exists
    if (!file_exists("data")) {
        if (!mkdir("data", 0755, true)) {
            error_log("Failed to create data directory");
            return false;
        }
    }
    
    $filepath = __DIR__ . "/../data/virtualcards." . $setid . ".dat";
    $serialized = serialize($mappings);
    
    $result = file_put_contents($filepath, $serialized);
    if ($result === false) {
        error_log("Failed to save virtual card mappings: $filepath");
        return false;
    }
    
    return true;
}

/**
 * Get card data from token
 * 
 * @param string $token Card access token
 * @return array|null Card data or null if not found
 */
function get_card_from_token($token) {
    // Validate token format
    if (!preg_match('/^[a-f0-9]{32}$/', $token)) {
        return null;
    }
    
    $mappings = load_virtual_card_mappings();
    
    if (!isset($mappings[$token])) {
        return null;
    }
    
    return $mappings[$token];
}

?>
