<?php
session_start();

// Database connection
$server = "localhost";
$username = "root";
$password = "";
$dbname = "projtest";

// Create connection
$con = mysqli_connect($server, $username, $password, $dbname);

// Check if connection is successful
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Trigger creation query
$trigger_sql = "
    CREATE TRIGGER IF NOT EXISTS log_user_logout
    AFTER DELETE ON projtest.cart
    FOR EACH ROW
    BEGIN
        INSERT INTO projtest.logout_logs (user_id, logout_time)
        VALUES (OLD.user_id, CURRENT_TIMESTAMP);
    END;
";

// Execute trigger creation query (this step creates the trigger)
if (mysqli_query($con, $trigger_sql)) {
    echo "Trigger created successfully.";
} else {
    // If the trigger already exists, you can ignore this error or handle it accordingly
    if (mysqli_errno($con) == 1050) {
        // 1050 is the error code for "Table already exists", which means the trigger already exists
        echo "Trigger already exists. Skipping creation.";
    } else {
        echo "Error creating trigger: " . mysqli_error($con);
    }
}

// Start a transaction for logout
mysqli_begin_transaction($con);

try {
    // If the user is logged in, perform the logout actions
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];

        // SQL to delete the cart for the logged-in user
        $delete_cart_sql = "DELETE FROM projtest.cart WHERE user_id = '$user_id'";

        if ($con->query($delete_cart_sql) !== TRUE) {
            throw new Exception("Error deleting cart: " . $con->error);
        }
    }

    // Commit the transaction
    mysqli_commit($con);
} catch (Exception $e) {
    // Rollback the transaction if an error occurs
    mysqli_rollback($con);
    echo "Transaction failed: " . $e->getMessage();
}

// Close the database connection
$con->close();

// Destroy session and unset all session variables
session_unset();
session_destroy();

// Redirect to the originating page or home page
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index3.php';
header("Location: $redirect");
exit();
?>
