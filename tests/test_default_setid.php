<?php
/**
 * Test script to validate default SET ID is 'A'
 * This test ensures the default SET ID in config/settings.php is always 'A'
 * to prevent regressions like the one introduced by the dropdown alignment fix.
 */

echo "Testing default SET ID configuration...\n\n";

// Include the settings file
$settings_file = __DIR__ . '/../config/settings.php';

if (!file_exists($settings_file)) {
    echo "✗ FAIL: settings.php not found at: $settings_file\n";
    exit(1);
}

// Read the file content
$content = file_get_contents($settings_file);

// Check if $setid='A'; exists in the file
if (preg_match("/\\\$setid\s*=\s*'A'\s*;/", $content)) {
    echo "✓ PASS: Default SET ID is correctly set to 'A'\n";
    
    // Also verify it's not set to 'B'
    if (preg_match("/\\\$setid\s*=\s*'B'\s*;/", $content)) {
        echo "✗ FAIL: Found conflicting SET ID set to 'B' in the file\n";
        exit(1);
    }
    
    echo "✓ PASS: No conflicting SET ID declarations found\n";
    echo "\n✅ All default SET ID tests passed!\n";
    exit(0);
} else {
    echo "✗ FAIL: Default SET ID is not set to 'A'\n";
    
    // Check what it's actually set to
    if (preg_match("/\\\$setid\s*=\s*'([^']*)'\s*;/", $content, $matches)) {
        echo "  Current value: \$setid='{$matches[1]}';\n";
        echo "  Expected value: \$setid='A';\n";
    } else {
        echo "  Could not find \$setid declaration in the file\n";
    }
    
    exit(1);
}
?>
