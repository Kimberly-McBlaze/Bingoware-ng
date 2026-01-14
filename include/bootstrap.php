<?php
/**
 * Bingoware-ng Bootstrap
 * Centralized initialization for all entrypoint pages
 * 
 * Loads in order:
 * 1. Configuration settings
 * 2. Constants
 * 3. Storage utilities
 * 4. Core functions
 * 5. Input helpers
 */

// Prevent direct access
if (!defined('BINGOWARE_BOOTSTRAP')) {
    define('BINGOWARE_BOOTSTRAP', true);
}

// Load configuration
if (file_exists(__DIR__ . "/../config/settings.php")) {
    include_once(__DIR__ . "/../config/settings.php");
}

// Load constants
if (file_exists(__DIR__ . "/constants.php")) {
    include_once(__DIR__ . "/constants.php");
}

// Load storage utilities
if (file_exists(__DIR__ . "/storage.php")) {
    include_once(__DIR__ . "/storage.php");
}

// Load core functions
if (file_exists(__DIR__ . "/functions.php")) {
    include_once(__DIR__ . "/functions.php");
}

// Load input helpers
if (file_exists(__DIR__ . "/input_helpers.php")) {
    include_once(__DIR__ . "/input_helpers.php");
}

// Load patterns functions
if (file_exists(__DIR__ . "/patterns.php")) {
    include_once(__DIR__ . "/patterns.php");
}

?>
