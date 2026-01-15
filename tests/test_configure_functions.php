<?php
/**
 * Test script to verify virtual_cards.php functions are properly loaded
 * and that configure.php doesn't have syntax or function availability issues
 */

// Simulate environment for configure.php
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['HTTPS'] = 'off';

// Load the configuration
require_once(__DIR__ . '/../config/settings.php');
require_once(__DIR__ . '/../include/functions.php');

echo "Testing virtual_cards.php functions...\n";

// Test that virtual_cards.php can be included
if (file_exists(__DIR__ . '/../include/virtual_cards.php')) {
    include_once(__DIR__ . '/../include/virtual_cards.php');
    echo "✓ virtual_cards.php included successfully\n";
} else {
    echo "✗ virtual_cards.php not found\n";
    exit(1);
}

// Test that required functions exist
$required_functions = [
    'has_virtual_stacks',
    'delete_all_virtual_stacks',
    'load_virtual_card_stacks',
    'save_virtual_card_stacks',
    'generate_virtual_cards',
];

$all_passed = true;
foreach ($required_functions as $func) {
    if (function_exists($func)) {
        echo "✓ Function '$func' exists\n";
    } else {
        echo "✗ Function '$func' does NOT exist\n";
        $all_passed = false;
    }
}

// Test basic functionality
echo "\nTesting basic functionality...\n";

// Test has_virtual_stacks (should not fatal even if no stacks exist)
try {
    $has_stacks = has_virtual_stacks();
    echo "✓ has_virtual_stacks() executed successfully (result: " . ($has_stacks ? "true" : "false") . ")\n";
} catch (Exception $e) {
    echo "✗ has_virtual_stacks() threw exception: " . $e->getMessage() . "\n";
    $all_passed = false;
}

// Test load_virtual_card_stacks
try {
    $stacks = load_virtual_card_stacks();
    echo "✓ load_virtual_card_stacks() executed successfully (count: " . count($stacks) . ")\n";
} catch (Exception $e) {
    echo "✗ load_virtual_card_stacks() threw exception: " . $e->getMessage() . "\n";
    $all_passed = false;
}

echo "\n";
if ($all_passed) {
    echo "✅ All tests passed!\n";
    exit(0);
} else {
    echo "❌ Some tests failed!\n";
    exit(1);
}
?>
