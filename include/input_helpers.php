<?php
/**
 * Input Validation and Sanitization Helpers
 * Provides lightweight validation and normalization for request inputs
 */

/**
 * Validate and sanitize an integer from request input
 * @param mixed $value The value to validate
 * @param int $min Minimum allowed value
 * @param int $max Maximum allowed value (optional)
 * @param int|null $default Default value if validation fails
 * @return int|null Sanitized integer or default/null
 */
function validate_int($value, $min = 0, $max = null, $default = null) {
    if ($value === null || $value === '') {
        return $default;
    }
    
    // Cast to int
    $int_value = filter_var($value, FILTER_VALIDATE_INT);
    
    if ($int_value === false) {
        return $default;
    }
    
    // Check minimum
    if ($int_value < $min) {
        return $default;
    }
    
    // Check maximum if provided
    if ($max !== null && $int_value > $max) {
        return $default;
    }
    
    return $int_value;
}

/**
 * Validate and sanitize a string from request input
 * @param mixed $value The value to validate
 * @param int $max_length Maximum allowed length
 * @param string $default Default value if validation fails
 * @return string Sanitized string or default
 */
function validate_string($value, $max_length = 255, $default = '') {
    if ($value === null) {
        return $default;
    }
    
    // Convert to string and trim
    $str = trim((string)$value);
    
    // Enforce max length
    if (strlen($str) > $max_length) {
        $str = substr($str, 0, $max_length);
    }
    
    return $str;
}

/**
 * Validate a pattern ID (alphanumeric with underscores)
 * @param mixed $value The value to validate
 * @return string|null Sanitized pattern ID or null
 */
function validate_pattern_id($value) {
    if ($value === null || $value === '') {
        return null;
    }
    
    $str = validate_string($value, 100);
    
    // Pattern IDs should be alphanumeric with underscores
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $str)) {
        return null;
    }
    
    return $str;
}

/**
 * Build a safe query string from an associative array
 * @param array $params Key-value pairs for query parameters
 * @return string URL-encoded query string (without leading ?)
 */
function build_query_string($params) {
    $filtered = [];
    
    foreach ($params as $key => $value) {
        if ($value !== null && $value !== '') {
            $filtered[$key] = $value;
        }
    }
    
    return http_build_query($filtered, '', '&', PHP_QUERY_RFC3986);
}

/**
 * Validate boolean value from request input
 * @param mixed $value The value to validate
 * @param bool $default Default value if validation fails
 * @return bool
 */
function validate_bool($value, $default = false) {
    if ($value === null || $value === '') {
        return $default;
    }
    
    // Handle string representations
    if (is_string($value)) {
        $lower = strtolower($value);
        if ($lower === 'true' || $lower === '1' || $lower === 'on' || $lower === 'yes') {
            return true;
        }
        if ($lower === 'false' || $lower === '0' || $lower === 'off' || $lower === 'no') {
            return false;
        }
    }
    
    return (bool)$value;
}

/**
 * Validate JSON string and decode it
 * @param mixed $value The JSON string to validate
 * @param mixed $default Default value if validation fails
 * @return mixed Decoded JSON or default
 */
function validate_json($value, $default = null) {
    if ($value === null || $value === '') {
        return $default;
    }
    
    $decoded = json_decode($value, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        return $default;
    }
    
    return $decoded;
}
