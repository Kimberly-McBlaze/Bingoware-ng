<?php
/**
 * Test script to validate batch_generate_sets function
 * This test ensures the batch generation feature works correctly
 */

echo "Testing batch_generate_sets function...\n\n";

// Include required files
require_once __DIR__ . '/../include/constants.php';
require_once __DIR__ . '/../config/settings.php';
require_once __DIR__ . '/../include/functions.php';

$test_passed = 0;
$test_failed = 0;

// Test 1: Function exists
echo "=== Test 1: Function exists ===\n";
if (function_exists('batch_generate_sets')) {
    echo "✓ PASS: batch_generate_sets function exists\n\n";
    $test_passed++;
} else {
    echo "✗ FAIL: batch_generate_sets function does not exist\n\n";
    $test_failed++;
    exit(1);
}

// Test 2: Invalid number of sets (too low)
echo "=== Test 2: Validation - Too few sets ===\n";
$result = batch_generate_sets(0, 10, 1, 'Test');
if (!$result['success'] && isset($result['error'])) {
    echo "✓ PASS: Correctly rejected 0 sets\n";
    echo "  Error message: {$result['error']}\n\n";
    $test_passed++;
} else {
    echo "✗ FAIL: Should reject 0 sets\n\n";
    $test_failed++;
}

// Test 3: Invalid number of sets (too high)
echo "=== Test 3: Validation - Too many sets ===\n";
$result = batch_generate_sets(101, 10, 1, 'Test');
if (!$result['success'] && isset($result['error'])) {
    echo "✓ PASS: Correctly rejected 101 sets\n";
    echo "  Error message: {$result['error']}\n\n";
    $test_passed++;
} else {
    echo "✗ FAIL: Should reject 101 sets\n\n";
    $test_failed++;
}

// Test 4: Invalid cards per set (too low)
echo "=== Test 4: Validation - Too few cards ===\n";
$result = batch_generate_sets(1, 0, 1, 'Test');
if (!$result['success'] && isset($result['error'])) {
    echo "✓ PASS: Correctly rejected 0 cards per set\n";
    echo "  Error message: {$result['error']}\n\n";
    $test_passed++;
} else {
    echo "✗ FAIL: Should reject 0 cards per set\n\n";
    $test_failed++;
}

// Test 5: Invalid freesquare mode
echo "=== Test 5: Validation - Invalid freesquare mode ===\n";
$result = batch_generate_sets(1, 10, 5, 'Test');
if (!$result['success'] && isset($result['error'])) {
    echo "✓ PASS: Correctly rejected invalid freesquare mode\n";
    echo "  Error message: {$result['error']}\n\n";
    $test_passed++;
} else {
    echo "✗ FAIL: Should reject invalid freesquare mode\n\n";
    $test_failed++;
}

// Test 6: Invalid SET ID format
echo "=== Test 6: Validation - Invalid SET ID format ===\n";
$result = batch_generate_sets(1, 10, 1, 'Test@#$');
if (!$result['success'] && isset($result['error'])) {
    echo "✓ PASS: Correctly rejected invalid SET ID format\n";
    echo "  Error message: {$result['error']}\n\n";
    $test_passed++;
} else {
    echo "✗ FAIL: Should reject invalid SET ID format\n\n";
    $test_failed++;
}

// Test 7: Generate single set (should not append -1)
echo "=== Test 7: Generate single set ===\n";
$result = batch_generate_sets(1, 5, 1, 'TestSingle');
if ($result['success'] && count($result['sets']) == 1) {
    $set = $result['sets'][0];
    if ($set['setid'] === 'TestSingle' && $set['success'] && $set['cards'] == 5) {
        echo "✓ PASS: Single set generated correctly\n";
        echo "  SET ID: {$set['setid']} (no -1 suffix)\n";
        echo "  Cards: {$set['cards']}\n";
        
        // Verify file exists
        $file = __DIR__ . '/../sets/set.TestSingle.dat';
        if (file_exists($file)) {
            echo "✓ PASS: Set file created\n";
            unlink($file); // Clean up
            echo "  (cleaned up test file)\n\n";
            $test_passed++;
        } else {
            echo "✗ FAIL: Set file not created\n\n";
            $test_failed++;
        }
    } else {
        echo "✗ FAIL: Single set details incorrect\n";
        echo "  Expected: setid='TestSingle', cards=5, success=true\n";
        echo "  Got: setid='{$set['setid']}', cards={$set['cards']}, success=" . ($set['success'] ? 'true' : 'false') . "\n\n";
        $test_failed++;
    }
} else {
    echo "✗ FAIL: Single set generation failed\n\n";
    $test_failed++;
}

// Test 8: Generate multiple sets (should append -1, -2, -3)
echo "=== Test 8: Generate multiple sets ===\n";
$result = batch_generate_sets(3, 5, 1, 'TestMulti');
if ($result['success'] && count($result['sets']) == 3) {
    $all_correct = true;
    for ($i = 0; $i < 3; $i++) {
        $set = $result['sets'][$i];
        $expected_id = 'TestMulti-' . ($i + 1);
        if ($set['setid'] !== $expected_id || !$set['success'] || $set['cards'] != 5) {
            $all_correct = false;
            echo "✗ FAIL: Set $i incorrect\n";
            echo "  Expected: setid='$expected_id', cards=5, success=true\n";
            echo "  Got: setid='{$set['setid']}', cards={$set['cards']}, success=" . ($set['success'] ? 'true' : 'false') . "\n";
        } else {
            echo "✓ SET {$set['setid']}: OK\n";
            
            // Clean up
            $file = __DIR__ . '/../sets/set.' . $set['setid'] . '.dat';
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }
    
    if ($all_correct) {
        echo "✓ PASS: All 3 sets generated correctly with numbered suffixes\n";
        echo "  (cleaned up test files)\n\n";
        $test_passed++;
    } else {
        echo "\n";
        $test_failed++;
    }
} else {
    echo "✗ FAIL: Multiple sets generation failed\n";
    if (isset($result['error'])) {
        echo "  Error: {$result['error']}\n";
    }
    echo "\n";
    $test_failed++;
}

// Test 9: Verify original setid is restored
echo "=== Test 9: Original setid restoration ===\n";
$original_setid = $setid;
$result = batch_generate_sets(2, 3, 1, 'TempSet');
if ($setid === $original_setid) {
    echo "✓ PASS: Original setid restored after batch generation\n";
    echo "  Original: $original_setid, Current: $setid\n";
    
    // Clean up
    for ($i = 1; $i <= 2; $i++) {
        $file = __DIR__ . '/../sets/set.TempSet-' . $i . '.dat';
        if (file_exists($file)) {
            unlink($file);
        }
    }
    echo "  (cleaned up test files)\n\n";
    $test_passed++;
} else {
    echo "✗ FAIL: Original setid not restored\n";
    echo "  Expected: $original_setid, Got: $setid\n\n";
    $test_failed++;
}

// Summary
echo "==================================================\n";
echo "TEST RESULTS SUMMARY\n";
echo "==================================================\n";
echo "Tests Run: " . ($test_passed + $test_failed) . "\n";
echo "Tests Passed: $test_passed\n";
echo "Tests Failed: $test_failed\n";
echo "\n";

if ($test_failed == 0) {
    echo "✓ ALL TESTS PASSED!\n";
    exit(0);
} else {
    echo "✗ SOME TESTS FAILED!\n";
    exit(1);
}
