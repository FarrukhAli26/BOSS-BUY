<?php
// Start session and ensure the user is an admin
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connection variables
$server = "localhost";
$username = "root";
$password = "";
$dbname = "projtest";

// Create a database connection
$con = mysqli_connect($server, $username, $password, $dbname);

// Check for connection success
if(!$con){
	die("Connection failed: " . mysqli_connect_error());
}

// Query to get all products
$sql = "SELECT product_id, product_name, price, stock_quantity, category_id, description FROM projtest.product";
$result = mysqli_query($con, $sql);

// Handle delete request
if (isset($_GET['delete'])) {
	$product_id = mysqli_real_escape_string($con, $_GET['delete']);

    // Delete query
	$sql_delete = "DELETE FROM projtest.product WHERE product_id = $product_id";

	if (mysqli_query($con, $sql_delete)) {
		echo "<div class='alert'>Product deleted successfully!</div>";
	} else {
		echo "<div class='alert'>Error deleting product: " . mysqli_error($con) . "</div>";
	}
}

// Handle add product request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
	$product_id = mysqli_real_escape_string($con, $_POST['product_id']);
	$product_name = mysqli_real_escape_string($con, $_POST['product_name']);
	$price = mysqli_real_escape_string($con, $_POST['price']);
	$stock_quantity = mysqli_real_escape_string($con, $_POST['stock_quantity']);
	$category_id = mysqli_real_escape_string($con, $_POST['category_id']);
	$description = mysqli_real_escape_string($con, $_POST['description']);

    // Insert query
	$sql_add = "INSERT INTO projtest.product (product_id, product_name, price, stock_quantity, category_id, description) 
	VALUES ('$product_id', '$product_name', '$price', '$stock_quantity', '$category_id', '$description')";

	if (mysqli_query($con, $sql_add)) {
		echo "<div class='alert'>New product added successfully!</div>";
	} else {
		echo "<div class='alert'>Error adding product: " . mysqli_error($con) . "</div>";
	}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin Panel</title>
	<style>
		/* Styles remain unchanged */
		body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; }
		h1, h2 { text-align: center; color: #333; }
		table { width: 80%; margin: 0 auto; border-collapse: collapse; margin-top: 20px; }
		table, th, td { border: 1px solid #ddd; }
		th, td { padding: 8px 12px; text-align: center; }
		th { background-color: #333; color: white; }
		td a { color: red; text-decoration: none; }
		td a:hover { text-decoration: underline; }
		form { width: 80%; margin: 20px auto; background-color: white; padding: 20px; border-radius: 8px; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); }
		form label { font-size: 1rem; margin-bottom: 5px; }
		form input, form select, form textarea { width: 100%; padding: 10px; margin: 10px 0; border-radius: 5px; border: 1px solid #ccc; }
		.alert { background-color: #4CAF50; color: white; padding: 10px; margin: 15px; text-align: center; border-radius: 5px; }
		.alert.error { background-color: #f44336; }
		.back-to-shop { margin-top: 4.5rem; margin-left: 160px; }
		.back-to-shop > a:hover { font-size: 20px; transition: all 0.5s ease; }
		.back-to-shop > a { transition: all 0.5s ease; text-decoration: none; }
		form select, form button {
			padding: 5px 10px;
			margin-right: 5px;
			border: 1px solid #ccc;
			border-radius: 5px;
			cursor: pointer;
		}
		form button {
			background-color: #4CAF50;
			color: white;
			border: none;
		}
		form button:hover {
			background-color: #45a049;
		}

	</style>
</head>
<body>

	<h1>Admin Dashboard</h1>

	<!-- Add New Product Form -->
	<h2>Add New Product</h2>
	<form method="post" action="admin.php">
		<label for="product_id">Product ID:</label>
		<input type="text" id="product_id" name="product_id" required>

		<label for="product_name">Product Name:</label>
		<input type="text" id="product_name" name="product_name" required>

		<label for="price">Price:</label>
		<input type="number" id="price" name="price" step="0.01" required>

		<label for="stock_quantity">Stock Quantity:</label>
		<input type="number" id="stock_quantity" name="stock_quantity" required>

		<label for="category_id">Category:</label>
		<select id="category_id" name="category_id" required>
			<option value="" disabled selected>Select a category</option>
			<option value="1">1 - WOMEN-CLOTHES</option>
			<option value="2">2 - WOMEN-ACCESSORIES</option>
			<option value="3">3 - MEN-CLOTHES</option>
			<option value="4">4 - MEN-ACCESSORIES</option>
		</select>

		<label for="description">Description:</label>
		<textarea id="description" name="description" required></textarea>

		<input type="submit" name="add_product" value="Add Product">
	</form>

	<!-- Display All Products -->
	<h2>Product List</h2>
	<?php
	if (mysqli_num_rows($result) > 0) {
		echo "<table>
		<tr>
		<th>Product ID</th>
		<th>Product Name</th>
		<th>Price</th>
		<th>Stock</th>
		<th>Category</th>
		<th>Description</th>
		<th>Actions</th>
		</tr>";

		while($row = mysqli_fetch_assoc($result)) {
			echo "<tr>
			<td>" . $row['product_id'] . "</td>
			<td>" . $row['product_name'] . "</td>
			<td>" . $row['price'] . "</td>
			<td>" . $row['stock_quantity'] . "</td>
			<td>" . $row['category_id'] . "</td>
			<td>" . $row['description'] . "</td>
			<td><a href='admin.php?delete=" . $row['product_id'] . "'>Delete</a></td>
			</tr>";
		}
		echo "</table>";
	} else {
		echo "<div class='alert error'>No products found!</div>";
	}
	?>

	<!-- Display All Users -->
	<?php
// Handle delete user request
if (isset($_GET['delete_user'])) {
    $user_id = mysqli_real_escape_string($con, $_GET['delete_user']);

    // Delete query
    $delete_user_query = "DELETE FROM projtest.user WHERE user_id = $user_id";
    if (mysqli_query($con, $delete_user_query)) {
        echo "<div class='alert'>User deleted successfully!</div>";
    } else {
        echo "<div class='alert error'>Error deleting user: " . mysqli_error($con) . "</div>";
    }
}
?>

<!-- Display All Users -->
<h2>User List</h2>
<?php
$user_query = "SELECT * FROM projtest.user";
$user_result = mysqli_query($con, $user_query);

if (mysqli_num_rows($user_result) > 0) {
    echo "<table>
    <tr>
    <th>User ID</th>
    <th>First Name</th>
    <th>Last Name</th>
    <th>Mobile</th>
    <th>City</th>
    <th>Country</th>
    <th>Address</th>
    <th>State/Province</th>
    <th>Email</th>
    <th>Actions</th>
    </tr>";

    while ($user = mysqli_fetch_assoc($user_result)) {
        echo "<tr>
        <td>" . $user['user_id'] . "</td>
        <td>" . $user['f_name'] . "</td>
        <td>" . $user['l_name'] . "</td>
        <td>" . $user['mobile'] . "</td>
        <td>" . $user['city'] . "</td>
        <td>" . $user['country'] . "</td>
        <td>" . $user['address'] . "</td>
        <td>" . $user['state_province'] . "</td>
        <td>" . $user['email'] . "</td>
        <td>
            <a href='admin.php?delete_user=" . $user['user_id'] . "' style='color: red;'>Delete</a>
        </td>
        </tr>";
    }
    echo "</table>";
} else {
    echo "<div class='alert error'>No users found!</div>";
}
?>


	<!-- Display All Reviews -->
	<h2>Reviews</h2>
	<?php
	$review_query = "SELECT * FROM projtest.reviews";
	$review_result = mysqli_query($con, $review_query);

	if (mysqli_num_rows($review_result) > 0) {
		echo "<table>
		<tr>
		<th>Review ID</th>
		<th>Product ID</th>
		<th>User ID</th>
		<th>Review Date</th>
		<th>Rating</th>
		<th>Comment</th>
		</tr>";

		while ($review = mysqli_fetch_assoc($review_result)) {
			echo "<tr>
			<td>" . $review['review_id'] . "</td>
			<td>" . $review['product_id'] . "</td>
			<td>" . $review['user_id'] . "</td>
			<td>" . $review['review_date'] . "</td>
			<td>" . $review['rating'] . "</td>
			<td>" . $review['comment'] . "</td>
			</tr>";
		}
		echo "</table>";
	} else {
		echo "<div class='alert error'>No reviews found!</div>";
	}
	?>

	<!-- Display All Orders -->
	<h2>Orders</h2>
	<?php
	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
		$order_id = mysqli_real_escape_string($con, $_POST['order_id']);
		$new_status = mysqli_real_escape_string($con, $_POST['status']);

    // Update the status in the database
		$update_status_query = "UPDATE projtest.orders SET status = '$new_status' WHERE order_id = $order_id";
		if (mysqli_query($con, $update_status_query)) {
			echo "<div class='alert'>Order status updated successfully!</div>";
		} else {
			echo "<div class='alert error'>Error updating status: " . mysqli_error($con) . "</div>";
		}
	}

// Fetch orders
	$order_query = "SELECT * FROM projtest.orders";
	$order_result = mysqli_query($con, $order_query);

	if (mysqli_num_rows($order_result) > 0) {
		echo "<table>
		<tr>
		<th>Order ID</th>
		<th>User ID</th>
		<th>Order Date</th>
		<th>Bill</th>
		<th>Address</th>
		<th>Status</th>
		<th>Update Status</th>
		</tr>";

		while ($order = mysqli_fetch_assoc($order_result)) {
			echo "<tr>
			<td>" . $order['order_id'] . "</td>
			<td>" . $order['user_id'] . "</td>
			<td>" . $order['order_date'] . "</td>
			<td>" . $order['bill'] . "</td>
			<td>" . $order['address'] . "</td>
			<td>" . $order['status'] . "</td>
			<td>
			<form method='post' style='display: inline-flex;'>
			<input type='hidden' name='order_id' value='" . $order['order_id'] . "'>
			<select name='status' required>
			<option value='Pending' " . ($order['status'] === 'Pending' ? 'selected' : '') . ">Pending</option>
			<option value='Processing' " . ($order['status'] === 'Processing' ? 'selected' : '') . ">Processing</option>
			<option value='Shipped' " . ($order['status'] === 'Shipped' ? 'selected' : '') . ">Shipped</option>
			<option value='Delivered' " . ($order['status'] === 'Delivered' ? 'selected' : '') . ">Delivered</option>
			<option value='Cancelled' " . ($order['status'] === 'Cancelled' ? 'selected' : '') . ">Cancelled</option>
			</select>
			<button type='submit' name='update_status'>Update</button>
			</form>
			</td>
			</tr>";
		}
		echo "</table>";
	} else {
		echo "<div class='alert error'>No orders found!</div>";
	}
	?>


	<!-- Display All Order Items -->
	<h2>Order Items</h2>
	<?php
	$order_item_query = "SELECT * FROM projtest.order_item";
	$order_item_result = mysqli_query($con, $order_item_query);

	if (mysqli_num_rows($order_item_result) > 0) {
		echo "<table>
		<tr>
		<th>Order Item ID</th>
		<th>Product ID</th>
		<th>Order ID</th>
		<th>Quantity</th>
		<th>Price</th>
		</tr>";

		while ($order_item = mysqli_fetch_assoc($order_item_result)) {
			echo "<tr>
			<td>" . $order_item['order_item_id'] . "</td>
			<td>" . $order_item['product_id'] . "</td>
			<td>" . $order_item['order_id'] . "</td>
			<td>" . $order_item['quantity'] . "</td>
			<td>" . $order_item['price'] . "</td>
			</tr>";
		}
		echo "</table>";
	} else {
		echo "<div class='alert error'>No order items found!</div>";
	}
	?>

	<!-- Back to Shop -->
	<div class="back-to-shop"><a href="index3.php">&leftarrow;</a><span class="text-muted">Back to shop</span></div>
	<br><br><br>

</body>
</html>
