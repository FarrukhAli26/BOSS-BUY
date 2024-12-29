<?php
// Enable error reporting for debugging purposes
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Get the JSON data sent from the frontend
$data = json_decode(file_get_contents('php://input'), true);

// Check if we have the necessary data
if (!$data || !isset($data['itemId']) || !isset($data['newPrice'])) {
    echo json_encode(["success" => false, "message" => "Invalid data received"]);
    exit;
}

$itemId = $data['itemId'];  // ID of the cart item
$newPrice = $data['newPrice'];  // New price for the item

// Log the incoming request data for debugging
error_log("Received update for itemId: $itemId with newPrice: $newPrice");

// Check if the price is a valid number (optional check)
if (!is_numeric($newPrice)) {
    echo json_encode(["success" => false, "message" => "Invalid price format"]);
    exit;
}

// Set connection variables
$server = "localhost";
$username = "root";
$password = "";
$dbname = "projtest";  // Your actual database name

// Create a database connection
$con = mysqli_connect($server, $username, $password, $dbname);

// Check connection
if (!$con) {
    echo json_encode(["success" => false, "message" => "Connection failed: " . mysqli_connect_error()]);
    exit;
}

// Prepare the SQL statement
$sql = "UPDATE cart_item SET price = ? WHERE cart_item_id = ?";  // Assuming cart_item_id is the correct column name

// Prepare and bind the statement
$stmt = $con->prepare($sql);
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Error preparing SQL statement: " . $con->error]);
    exit;
}

$stmt->bind_param("di", $newPrice, $itemId);  // "d" for double (newPrice) and "i" for integer (itemId)

// Execute the query
if ($stmt->execute()) {
    // Successfully updated the price
    echo json_encode(["success" => true, "message" => "Price updated successfully"]);
} else {
    // Error updating the price
    echo json_encode(["success" => false, "message" => "Failed to update price: " . $stmt->error]);
}

// Close the statement and connection
$stmt->close();
$con->close();
?>
