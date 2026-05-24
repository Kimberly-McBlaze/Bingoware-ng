<?php

echo "Testing theme functionality removal...\n\n";

$root = dirname(__DIR__);
$checks = [
    [
        'file' => "$root/menu.php",
        'must_not_contain' => 'themes.php',
        'message' => 'Themes menu link removed'
    ],
    [
        'file' => "$root/header.php",
        'must_not_contain' => 'include/color-transform.js',
        'message' => 'Color transform script removed from header'
    ],
    [
        'file' => "$root/flashboard.php",
        'must_not_contain' => 'api/themes.php',
        'message' => 'Flashboard no longer fetches theme API'
    ],
    [
        'file' => "$root/include/modern-ui.js",
        'must_not_contain' => 'api/themes.php',
        'message' => 'Theme manager no longer loads theme API'
    ],
];

$passed = 0;
$failed = 0;

foreach ($checks as $check) {
    $content = file_get_contents($check['file']);
    if ($content === false) {
        echo "✗ FAIL: Could not read {$check['file']}\n";
        $failed++;
        continue;
    }

    if (str_contains($content, $check['must_not_contain'])) {
        echo "✗ FAIL: {$check['message']}\n";
        echo "  Found forbidden text: {$check['must_not_contain']}\n";
        $failed++;
    } else {
        echo "✓ PASS: {$check['message']}\n";
        $passed++;
    }
}

echo "\nChecking removed files...\n";

$removedFiles = [
    "$root/themes.php",
    "$root/api/themes.php",
    "$root/include/themes-ui.js",
];

foreach ($removedFiles as $file) {
    if (file_exists($file)) {
        echo "✗ FAIL: File still exists: " . basename($file) . "\n";
        $failed++;
    } else {
        echo "✓ PASS: File removed: " . basename($file) . "\n";
        $passed++;
    }
}

echo "\n==================================================\n";
echo "TEST RESULTS SUMMARY\n";
echo "==================================================\n";
echo "Tests Passed: $passed\n";
echo "Tests Failed: $failed\n\n";

if ($failed > 0) {
    echo "✗ SOME TESTS FAILED!\n";
    exit(1);
}

echo "✓ ALL TESTS PASSED!\n";
