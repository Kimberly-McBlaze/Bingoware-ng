<?php
/**
 * File-backed Storage Utility Layer
 * Centralizes safe file operations for Bingoware-ng
 * 
 * Provides:
 * - Data directory management
 * - Safe JSON read/write with error handling
 * - Atomic write operations (temp file + rename)
 */

/**
 * Ensure data directory exists with proper permissions
 * 
 * @param string $path Directory path (default: "data")
 * @return bool True on success, false on failure
 */
function ensure_data_dir($path = "data") {
    if (file_exists($path)) {
        if (!is_dir($path)) {
            error_log("Path exists but is not a directory: $path");
            return false;
        }
        return true;
    }
    
    if (!mkdir($path, 0755, true)) {
        error_log("Failed to create directory: $path");
        return false;
    }
    
    return true;
}

/**
 * Read JSON file with error handling
 * 
 * @param string $filepath Path to JSON file
 * @param mixed $default Default value if file doesn't exist or is invalid
 * @return mixed Decoded JSON data or default value
 */
function read_json($filepath, $default = null) {
    if (!file_exists($filepath)) {
        return $default;
    }
    
    $contents = file_get_contents($filepath);
    if ($contents === false) {
        error_log("Failed to read file: $filepath");
        return $default;
    }
    
    $data = json_decode($contents, true);
    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        error_log("Failed to parse JSON from $filepath: " . json_last_error_msg());
        return $default;
    }
    
    return $data;
}

/**
 * Write JSON file with error handling
 * 
 * @param string $filepath Path to JSON file
 * @param mixed $data Data to encode as JSON
 * @param bool $pretty Use pretty printing (default: true)
 * @return bool True on success, false on failure
 */
function write_json($filepath, $data, $pretty = true) {
    $flags = $pretty ? JSON_PRETTY_PRINT : 0;
    $json = json_encode($data, $flags);
    
    if ($json === false) {
        error_log("Failed to encode JSON for $filepath: " . json_last_error_msg());
        return false;
    }
    
    $result = file_put_contents($filepath, $json);
    if ($result === false) {
        error_log("Failed to write file: $filepath");
        return false;
    }
    
    return true;
}

/**
 * Atomic write to JSON file (temp file + rename)
 * Reduces risk of partial writes or corruption
 * 
 * @param string $filepath Path to JSON file
 * @param mixed $data Data to encode as JSON
 * @param bool $pretty Use pretty printing (default: true)
 * @return bool True on success, false on failure
 */
function atomic_write_json($filepath, $data, $pretty = true) {
    $flags = $pretty ? JSON_PRETTY_PRINT : 0;
    $json = json_encode($data, $flags);
    
    if ($json === false) {
        error_log("Failed to encode JSON for $filepath: " . json_last_error_msg());
        return false;
    }
    
    // Create temp file in same directory as target
    $dir = dirname($filepath);
    $temp_file = tempnam($dir, 'tmp_');
    
    if ($temp_file === false) {
        error_log("Failed to create temp file in directory: $dir");
        return false;
    }
    
    // Write to temp file
    $result = file_put_contents($temp_file, $json);
    if ($result === false) {
        error_log("Failed to write temp file: $temp_file");
        if (file_exists($temp_file)) {
            unlink($temp_file);
        }
        return false;
    }
    
    // Atomic rename
    if (!rename($temp_file, $filepath)) {
        error_log("Failed to rename $temp_file to $filepath");
        if (file_exists($temp_file)) {
            unlink($temp_file);
        }
        return false;
    }
    
    return true;
}

/**
 * Read serialized PHP file with error handling
 * 
 * @param string $filepath Path to serialized file
 * @param mixed $default Default value if file doesn't exist or is invalid
 * @return mixed Unserialized data or default value
 */
function read_serialized($filepath, $default = null) {
    if (!file_exists($filepath)) {
        return $default;
    }
    
    $contents = file_get_contents($filepath);
    if ($contents === false) {
        error_log("Failed to read file: $filepath");
        return $default;
    }
    
    $data = unserialize($contents);
    if ($data === false && $contents !== serialize(false)) {
        error_log("Failed to unserialize data from: $filepath");
        return $default;
    }
    
    return $data;
}

/**
 * Write serialized PHP file with error handling
 * 
 * @param string $filepath Path to serialized file
 * @param mixed $data Data to serialize
 * @return bool True on success, false on failure
 */
function write_serialized($filepath, $data) {
    $serialized = serialize($data);
    
    $result = file_put_contents($filepath, $serialized);
    if ($result === false) {
        error_log("Failed to write file: $filepath");
        return false;
    }
    
    return true;
}

?>
