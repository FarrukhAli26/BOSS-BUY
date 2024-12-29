<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$server = "localhost";
$username = "root";
$password = "";
$dbname = "projtest";
$con = mysqli_connect($server, $username, $password, $dbname);

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.php?redirect=my_orders.php");
    exit();
}

// Fetch user orders
$user_id = mysqli_real_escape_string($con, $_SESSION['user_id']);

// Create the view if it doesn't exist (this should be done only once, ideally during initialization)
$sql = "
CREATE VIEW IF NOT EXISTS view_user_orders AS
SELECT 
    o.order_id, 
    o.order_date, 
    o.bill, 
    oi.product_id, 
    p.product_name, 
    oi.quantity, 
    oi.price, 
    CONCAT(u.f_name, ' ', u.l_name) AS full_name,
    o.user_id  -- Ensure user_id is included in the view
FROM 
    projtest.orders o
INNER JOIN 
    projtest.order_item oi ON o.order_id = oi.order_id
INNER JOIN 
    projtest.product p ON oi.product_id = p.product_id
INNER JOIN 
    projtest.user u ON o.user_id = u.user_id
ORDER BY 
    o.order_id ASC;
";

// Execute the CREATE VIEW query (should only be executed once, preferably during setup)
if (mysqli_query($con, $sql)) {
    // View creation successful or already exists
} else {
    echo "Error creating view: " . mysqli_error($con);
}

// Proceed with the SELECT query to get orders for a specific user
$sql2 = "SELECT * FROM view_user_orders WHERE user_id = $user_id";

// Execute the SELECT query
$result = mysqli_query($con, $sql2);

// Initialize a variable to keep track of the last order_id
$last_order_id = null;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; }
        h1 { text-align: center; color: #333; }
        table { width: 80%; margin: 20px auto; border-collapse: collapse; background: white; }
        th, td { border: 1px solid #ddd; padding: 8px 12px; text-align: center; }
        th { background-color: #333; color: white; }
        .alert { text-align: center; margin: 20px; padding: 10px; background-color: #f44336; color: white; }
        .back-link { text-align: center; margin: 20px; }
        .back-link a { color: #919191; text-decoration: none; }
        .back-link a:hover { text-decoration: none; }
        .back-link > a:hover{
            font-size: 20px;
            transition: all 0.5s ease ; 
            color: black;
        }
        .back-link > a{
            transition: all 0.5s ease;
        }
    </style>
</head>
<body>

    <h1>My Orders</h1>

    <?php
    if (mysqli_num_rows($result) > 0) {
        echo "<table>
        <tr>
        <th>Full Name</th>
        <th>Order ID</th>
        <th>Order Date</th>
        <th>Bill</th>
        <th>Product Name</th>
        <th>Quantity</th>
        <th>Price</th>
        </tr>";

        // Loop through the result set
        while ($row = mysqli_fetch_assoc($result)) {
            // Check if the current order_id is different from the last one
            if ($last_order_id != $row['order_id']) {
                // New order, display order details
                echo "<tr>
                <td>{$row['full_name']}</td>
                <td>{$row['order_id']}</td>
                <td>{$row['order_date']}</td>
                <td>$" . number_format($row['bill'], 2) . "</td>
                <td>{$row['product_name']}</td>
                <td>{$row['quantity']}</td>
                <td>$" . number_format($row['price'], 2) . "</td>
                </tr>";
            } else {
                // Same order, only display product details, leave other fields empty
                echo "<tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>{$row['product_name']}</td>
                <td>{$row['quantity']}</td>
                <td>$" . number_format($row['price'], 2) . "</td>
                </tr>";
            }

            // Update last_order_id
            $last_order_id = $row['order_id'];
        }

        echo "</table>";
    } else {
        echo "<div class='alert'>No orders found!</div>";
    }
    ?>

    <div class="back-link">
        <a href="index3.php">&leftarrow; Back to Shop</a>
    </div>

</body>
</html>
