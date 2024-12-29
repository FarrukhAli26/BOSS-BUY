<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);
$itemId = $data['itemId'];

// Database connection
$server = "localhost";
$username = "root";
$password = "";
$con = mysqli_connect($server, $username, $password, "projtest");

if (!$con) {
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . mysqli_connect_error()]);
    exit;
}

// Delete the item from the cart
$query = "DELETE FROM projtest.cart_item WHERE cart_item_id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $itemId);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to remove item: ' . $stmt->error]);
}

$stmt->close();
$con->close();
?>
