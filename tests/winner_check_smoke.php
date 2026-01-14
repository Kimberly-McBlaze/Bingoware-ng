#!/usr/bin/env php
<?php
/**
 * Winner Check Smoke Tests
 * 
 * Basic test harness to validate the pure winner checking functions.
 * Run manually: php tests/winner_check_smoke.php
 */

// Load the winner check module
require_once(__DIR__ . '/../include/winner_check.php');

// Test counter
$tests_run = 0;
$tests_passed = 0;

/**
 * Helper function to create a test card
 */
function create_test_card($checked_positions = []) {
    $card = [];
    for ($col = 0; $col < 5; $col++) {
        for ($row = 0; $row < 5; $row++) {
            $card[$col][$row] = [
                'number' => ($col * 15) + $row + 1,
                'checked' => false
            ];
        }
    }
    
    // Mark specified positions as checked
    foreach ($checked_positions as $pos) {
        $card[$pos[0]][$pos[1]]['checked'] = true;
    }
    
    return $card;
}

/**
 * Assert function
 */
function test_assert($condition, $message) {
    global $tests_run, $tests_passed;
    $tests_run++;
    
    if ($condition) {
        $tests_passed++;
        echo "✓ PASS: $message\n";
        return true;
    } else {
        echo "✗ FAIL: $message\n";
        return false;
    }
}

// ===========================================
// Test 1: Check horizontal row win
// ===========================================
echo "\n=== Test 1: Horizontal Row Win ===\n";
$card = create_test_card([
    [0, 0], [1, 0], [2, 0], [3, 0], [4, 0]  // Top row
]);
$pattern = ['is_special' => true];
$result = check_card_matches_pattern($card, $pattern);
test_assert($result === true, "Top row should win with special pattern");

// ===========================================
// Test 2: Check vertical column win
// ===========================================
echo "\n=== Test 2: Vertical Column Win ===\n";
$card = create_test_card([
    [2, 0], [2, 1], [2, 2], [2, 3], [2, 4]  // Middle column
]);
$pattern = ['is_special' => true];
$result = check_card_matches_pattern($card, $pattern);
test_assert($result === true, "Middle column should win with special pattern");

// ===========================================
// Test 3: Check diagonal win (top-left to bottom-right)
// ===========================================
echo "\n=== Test 3: Diagonal Win (TL to BR) ===\n";
$card = create_test_card([
    [0, 0], [1, 1], [2, 2], [3, 3], [4, 4]
]);
$pattern = ['is_special' => true];
$result = check_card_matches_pattern($card, $pattern);
test_assert($result === true, "Diagonal TL-BR should win with special pattern");

// ===========================================
// Test 4: Check diagonal win (top-right to bottom-left)
// ===========================================
echo "\n=== Test 4: Diagonal Win (TR to BL) ===\n";
$card = create_test_card([
    [0, 4], [1, 3], [2, 2], [3, 1], [4, 0]
]);
$pattern = ['is_special' => true];
$result = check_card_matches_pattern($card, $pattern);
test_assert($result === true, "Diagonal TR-BL should win with special pattern");

// ===========================================
// Test 5: Check incomplete pattern (should not win)
// ===========================================
echo "\n=== Test 5: Incomplete Pattern (No Win) ===\n";
$card = create_test_card([
    [0, 0], [1, 0], [2, 0], [3, 0]  // Missing one square
]);
$pattern = ['is_special' => true];
$result = check_card_matches_pattern($card, $pattern);
test_assert($result === false, "Incomplete row should NOT win");

// ===========================================
// Test 6: Grid-based pattern - Four Corners
// ===========================================
echo "\n=== Test 6: Grid Pattern (Four Corners) ===\n";
$card = create_test_card([
    [0, 0], [0, 4], [4, 0], [4, 4]  // Four corners
]);
$pattern = [
    'is_special' => false,
    'grid' => [
        ['col' => 0, 'row' => 0],
        ['col' => 0, 'row' => 4],
        ['col' => 4, 'row' => 0],
        ['col' => 4, 'row' => 4]
    ]
];
$result = check_card_matches_pattern($card, $pattern);
test_assert($result === true, "Four corners should win with grid pattern");

// ===========================================
// Test 7: Grid-based pattern - Incomplete
// ===========================================
echo "\n=== Test 7: Grid Pattern Incomplete (No Win) ===\n";
$card = create_test_card([
    [0, 0], [0, 4], [4, 0]  // Missing one corner
]);
$pattern = [
    'is_special' => false,
    'grid' => [
        ['col' => 0, 'row' => 0],
        ['col' => 0, 'row' => 4],
        ['col' => 4, 'row' => 0],
        ['col' => 4, 'row' => 4]
    ]
];
$result = check_card_matches_pattern($card, $pattern);
test_assert($result === false, "Incomplete four corners should NOT win");

// ===========================================
// Test 8: Check multiple cards with check_winners
// ===========================================
echo "\n=== Test 8: Multiple Cards Check ===\n";
$cards = [
    create_test_card([[0, 0], [1, 0], [2, 0], [3, 0], [4, 0]]),  // Card 0: top row win
    create_test_card([[0, 1], [1, 1], [2, 1], [3, 1]]),          // Card 1: no win
    create_test_card([[0, 0], [1, 1], [2, 2], [3, 3], [4, 4]])   // Card 2: diagonal win
];
$patterns = [['is_special' => true]];
$winners = check_winners($cards, $patterns, 3);

test_assert(isset($winners[0][0]) && $winners[0][0] === true, "Card 0 should be a winner");
test_assert(!isset($winners[1][0]) || $winners[1][0] === false, "Card 1 should NOT be a winner");
test_assert(isset($winners[2][0]) && $winners[2][0] === true, "Card 2 should be a winner");

// ===========================================
// Test 9: Count winning cards
// ===========================================
echo "\n=== Test 9: Count Winning Cards ===\n";
$count = count_winning_cards($winners);
test_assert($count === 2, "Should count 2 winning cards (got $count)");

// ===========================================
// Test 10: Get winning card numbers
// ===========================================
echo "\n=== Test 10: Get Winning Card Numbers ===\n";
$winning_numbers = get_winning_card_numbers($winners);
test_assert(in_array(0, $winning_numbers), "Card 0 should be in winning numbers");
test_assert(!in_array(1, $winning_numbers), "Card 1 should NOT be in winning numbers");
test_assert(in_array(2, $winning_numbers), "Card 2 should be in winning numbers");

// ===========================================
// Test Results Summary
// ===========================================
echo "\n" . str_repeat("=", 50) . "\n";
echo "TEST RESULTS SUMMARY\n";
echo str_repeat("=", 50) . "\n";
echo "Tests Run: $tests_run\n";
echo "Tests Passed: $tests_passed\n";
echo "Tests Failed: " . ($tests_run - $tests_passed) . "\n";

if ($tests_passed === $tests_run) {
    echo "\n✓ ALL TESTS PASSED!\n\n";
    exit(0);
} else {
    echo "\n✗ SOME TESTS FAILED\n\n";
    exit(1);
}

?>
