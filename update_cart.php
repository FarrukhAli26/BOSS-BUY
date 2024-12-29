<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Get POST data (JSON payload)
$data = json_decode(file_get_contents("php://input"), true);
$itemId = $data['itemId']; // cart_item_id from frontend
$quantity = $data['quantity']; // Updated quantity

// Database connection
$server = "localhost";
$username = "root";
$password = "";
$con = mysqli_connect($server, $username, $password, "projtest");

// Check if connection is successful
if (!$con) {
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . mysqli_connect_error()]);
    exit;
}

// Update the quantity of the item in the cart
$query = "UPDATE cart_item SET quantity = ? WHERE cart_item_id = ?";  // Make sure 'cart_item_id' is the correct column name
$stmt = $con->prepare($query);

if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Query preparation failed: ' . $con->error]);
    exit;
}

// Bind the parameters (i for integer)
$stmt->bind_param("ii", $quantity, $itemId); // 'ii' means two integers: quantity and cart_item_id

// Execute the statement
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Quantity updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update quantity: ' . $stmt->error]);
}

// Close the statement and connection
$stmt->close();
$con->close();
?>
