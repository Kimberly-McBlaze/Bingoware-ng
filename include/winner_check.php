<?php
/**
 * Winner Checking Module (Pure Functions)
 * 
 * Provides deterministic winner checking logic as pure functions.
 * Functions take explicit inputs and return deterministic outputs
 * without side effects (no file I/O, no globals).
 */

/**
 * Check if a single card matches a pattern
 * Pure function - no side effects, deterministic output
 * 
 * @param array $card 5x5 card grid with 'checked' status
 * @param array $pattern Pattern definition with 'is_special' and optionally 'grid'
 * @return bool True if card matches pattern, false otherwise
 */
function check_card_matches_pattern($card, $pattern) {
    // Special pattern (Normal bingo: any row, column, or diagonal)
    if ($pattern['is_special']) {
        return check_special_pattern($card);
    }
    
    // Grid-based pattern
    return check_grid_pattern($card, $pattern['grid']);
}

/**
 * Check special pattern (Normal bingo)
 * Checks for any complete row, column, or diagonal
 * 
 * @param array $card 5x5 card grid
 * @return bool True if any row, column, or diagonal is complete
 */
function check_special_pattern($card) {
    // Check rows and columns
    for ($c = 0; $c < 5; $c++) {
        $rowbingo = true;
        $colbingo = true;
        
        for ($r = 0; $r < 5; $r++) {
            if (!$card[$c][$r]["checked"]) {
                $colbingo = false;
            }
            if (!$card[$r][$c]["checked"]) {
                $rowbingo = false;
            }
        }
        
        if ($rowbingo || $colbingo) {
            return true;
        }
    }
    
    // Check diagonals
    $bingod1 = true; // Top-left to bottom-right
    $bingod2 = true; // Top-right to bottom-left
    
    for ($d = 0; $d < 5; $d++) {
        if (!$card[$d][$d]["checked"]) {
            $bingod1 = false;
        }
        if (!$card[$d][4 - $d]["checked"]) {
            $bingod2 = false;
        }
    }
    
    return ($bingod1 || $bingod2);
}

/**
 * Check grid-based pattern
 * Verifies all required squares in the pattern are checked
 * 
 * @param array $card 5x5 card grid
 * @param array $grid Array of required squares [{col, row}, ...]
 * @return bool True if all required squares are checked
 */
function check_grid_pattern($card, $grid) {
    if (empty($grid)) {
        return false;
    }
    
    foreach ($grid as $square) {
        if (!$card[$square['col']][$square['row']]["checked"]) {
            return false;
        }
    }
    
    return true;
}

/**
 * Check all cards against all enabled patterns
 * Pure function that returns winners matrix without side effects
 * 
 * @param array $cards Array of card grids
 * @param array $patterns Array of enabled patterns
 * @param int $numberinplay Number of cards in play (limit checking)
 * @param array|null $previous_winners Previous winners matrix (optional)
 * @return array 2D array [card_index][pattern_index] => bool
 */
function check_winners($cards, $patterns, $numberinplay, $previous_winners = null) {
    $winners = [];
    $numcards = count($cards);
    $pattern_list = array_values($patterns); // Re-index from 0
    
    // Check each card
    for ($n = 0; $n < min($numberinplay, $numcards); $n++) {
        // Check each pattern
        for ($p = 0; $p < count($pattern_list); $p++) {
            // Skip if already marked as winner in previous check
            if (isset($previous_winners[$n][$p]) && $previous_winners[$n][$p]) {
                $winners[$n][$p] = true;
                continue;
            }
            
            // Check if card matches pattern
            $winners[$n][$p] = check_card_matches_pattern($cards[$n], $pattern_list[$p]);
        }
    }
    
    return $winners;
}

/**
 * Count total winners from winners matrix
 * 
 * @param array $winners Winners matrix from check_winners()
 * @return int Total number of unique winning cards
 */
function count_winning_cards($winners) {
    if (empty($winners)) {
        return 0;
    }
    
    $winning_cards = [];
    
    foreach ($winners as $card_index => $patterns) {
        foreach ($patterns as $pattern_index => $is_winner) {
            if ($is_winner) {
                $winning_cards[$card_index] = true;
                break; // Card only needs to win once
            }
        }
    }
    
    return count($winning_cards);
}

/**
 * Get list of winning card numbers
 * 
 * @param array $winners Winners matrix from check_winners()
 * @return array Array of winning card indices
 */
function get_winning_card_numbers($winners) {
    if (empty($winners)) {
        return [];
    }
    
    $winning_cards = [];
    
    foreach ($winners as $card_index => $patterns) {
        foreach ($patterns as $pattern_index => $is_winner) {
            if ($is_winner) {
                $winning_cards[] = $card_index;
                break; // Card only needs to win once
            }
        }
    }
    
    return $winning_cards;
}

?>
