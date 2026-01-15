<?php
/**
 * Virtual Stacks API Endpoint
 * Handles JSON requests for virtual bingo card stack operations
 * 
 * Methods:
 * - POST /api/virtual_stacks.php (delete_stack_id) - Delete a stack
 */

// Load bootstrap
include_once(__DIR__ . "/../include/bootstrap.php");
include_once(__DIR__ . "/../include/virtual_cards.php");

// Set JSON content type
header('Content-Type: application/json');

// Get HTTP method
$method = $_SERVER['REQUEST_METHOD'];

// Delete stack
if ($method === 'POST' && isset($_POST['delete_stack_id'])) {
    $stack_id = $_POST['delete_stack_id'];
    
    // Function handles all validation
    $result = delete_virtual_stack($stack_id);
    echo json_encode($result);
    exit;
}

// Invalid request
echo json_encode(['success' => false, 'error' => 'Invalid request']);
exit;

?>
