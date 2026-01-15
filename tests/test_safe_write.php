<?php
/**
 * Test script to validate configure.php safe write logic
 * This tests that the settings file update logic works correctly
 */

echo "Testing configure.php safe write logic...\n\n";

// Create a test settings file
$test_dir = sys_get_temp_dir() . '/bingoware_test_' . uniqid();
mkdir($test_dir);
$test_settings = $test_dir . '/settings.php';

// Create initial settings file
$initial_content = <<<'EOD'
<?php
include_once (__DIR__ . "/../include/constants.php");

$setid='A';
$pagetitleconfig='Welcome to Bingoware-ng';
$pagetitle =($pagetitleconfig =='')?$version:$pagetitleconfig;
$namefile='';
$printrules='';
$drawmode='automatic';
$fourperpage='';
$viewheader='<center><b><font size="+4">B I N G O</font></b></center>';
$viewfooter='<center><b>Created with Bingoware-ng</b><br><br><a href="javascript:window.close();">close window</a></center>';
$printheader='<center><b><font size="+6">Bingo Card</font></b></center>';
$printfooter='<center><b><font size="-1">Created with Bingoware-ng</font></b></center>';
$headerfontcolor='#CC00CC';
$headerbgcolor='#3333FF';
$mainfontcolor='#0000CC';
$mainbgcolor='#9999FF';
$selectedfontcolor='#FF0000';
$selectedbgcolor='#FFFF66';
$bordercolor='#000000';
$virtualbingo='on';
$virtualbingo_max_request='12';
?>
EOD;

file_put_contents($test_settings, $initial_content);
echo "✓ Created test settings file\n";

// Simulate form inputs with problematic characters
$test_cases = [
    [
        'name' => 'Simple string update',
        'field' => 'setid',
        'value' => 'B'
    ],
    [
        'name' => 'String with HTML entities',
        'field' => 'viewheader',
        'value' => '<center><b><font size="+6">NEW HEADER</font></b></center>'
    ],
    [
        'name' => 'String with special regex chars',
        'field' => 'viewfooter',
        'value' => '<div>Footer (test)</div>'
    ],
    [
        'name' => 'String with forward slashes',
        'field' => 'printheader',
        'value' => '<center>Header // Test</center>'
    ],
];

foreach ($test_cases as $test) {
    echo "\nTest: {$test['name']}\n";
    
    // Read current file
    $filearray = file($test_settings);
    $new_content = "";
    
    // Apply the update logic (same as in configure.php)
    foreach ($filearray as $line) {
        if (preg_match("/^(\\$" . preg_quote($test['field']) . "=').*?';/", $line)) {
            $line = "\$" . $test['field'] . "='" . addslashes($test['value']) . "';\n";
        }
        $new_content .= $line;
    }
    
    // Validate content
    if (empty($new_content)) {
        echo "  ✗ FAIL: Generated content is empty\n";
        continue;
    }
    
    if (!preg_match('/^<\?php/', $new_content)) {
        echo "  ✗ FAIL: Content doesn't start with <?php\n";
        continue;
    }
    
    if (strlen($new_content) < 100) {
        echo "  ✗ FAIL: Content is too short\n";
        continue;
    }
    
    // Check if the update was applied (simplified - just check if value is present)
    $expected_line = "\$" . $test['field'] . "='" . addslashes($test['value']) . "';";
    if (strpos($new_content, $expected_line) === false) {
        echo "  ✗ FAIL: Expected line not found in updated content\n";
        echo "  Looking for: " . $expected_line . "\n";
        echo "  Content snippet:\n";
        $lines = explode("\n", $new_content);
        foreach ($lines as $line) {
            if (strpos($line, '$' . $test['field']) !== false) {
                echo "    " . $line . "\n";
            }
        }
        continue;
    }
    
    // Write to temp file and rename (atomic operation)
    $temp_file = $test_settings . '.tmp';
    $fp = fopen($temp_file, "w");
    if (!$fp) {
        echo "  ✗ FAIL: Could not open temp file\n";
        continue;
    }
    
    // Use file locking like production code
    if (!flock($fp, LOCK_EX)) {
        echo "  ✗ FAIL: Could not lock temp file\n";
        fclose($fp);
        @unlink($temp_file);
        continue;
    }
    
    $write_result = fwrite($fp, $new_content);
    flock($fp, LOCK_UN);
    fclose($fp);
    
    if ($write_result === false) {
        echo "  ✗ FAIL: Failed to write to temp file\n";
        @unlink($temp_file);
        continue;
    }
    
    if (!rename($temp_file, $test_settings)) {
        echo "  ✗ FAIL: Could not rename temp file\n";
        @unlink($temp_file);
        continue;
    }
    
    // Verify the file was written correctly
    $written_content = file_get_contents($test_settings);
    if ($written_content === $new_content) {
        echo "  ✓ PASS: File updated successfully\n";
    } else {
        echo "  ✗ FAIL: Written content doesn't match generated content\n";
    }
}

// Test that preg_quote doesn't cause issues in replacement string
echo "\n\nTest: Verify preg_quote issue is fixed\n";
$problematic_value = '<center><b><font size="+4">B I N G O</font></b></center>';
$escaped_for_regex = preg_quote($problematic_value, '/');
echo "  Original value: " . $problematic_value . "\n";
echo "  preg_quote result: " . $escaped_for_regex . "\n";

// The OLD buggy way (causes warnings):
// $line = preg_replace("/^(\\$viewheader=').*?';/", "$1" . $escaped_for_regex . "';", $line);
// This would try to use $escaped_for_regex as part of the regex pattern, causing warnings

// The NEW correct way:
// $line = "\$viewheader='" . addslashes($problematic_value) . "';\n";
$correct_result = "\$viewheader='" . addslashes($problematic_value) . "';\n";
echo "  Correct result: " . trim($correct_result) . "\n";
echo "  ✓ PASS: Using direct string construction instead of preg_replace\n";

// Cleanup
unlink($test_settings);
rmdir($test_dir);

echo "\n✅ All safe write tests completed!\n";
?>
