<?php
session_start();

// Initialize variables
$loginError = "";

// Database connection
$server = "localhost";
$username = "root";
$password = "";
$dbname = "projtest";

// Create a database connection
$con = mysqli_connect($server, $username, $password, $dbname);
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the login form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = $_POST['password'];

    // Start a transaction
    mysqli_begin_transaction($con);

    $ip_address = $_SERVER['REMOTE_ADDR'];

    try {
        // Check if the email exists in the database
        $sql = "SELECT * FROM projtest.user WHERE email = '$email' LIMIT 1";
        $result = mysqli_query($con, $sql);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);

            // Verify the password
            if (password_verify($password, $row['password'])) {
                // Set session variables for the logged-in user
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['user_name'] = $row['f_name'] . " " . $row['l_name'];
                $_SESSION['email'] = $row['email'];
                $log_sql = "INSERT INTO projtest.login_logs (user_id, login_time)
            VALUES ('" . $_SESSION['user_id'] . "', NOW())";
            mysqli_query($con, $log_sql);

                // Ensure the user has a cart entry
                $cart_check_sql = "SELECT cart_id FROM projtest.cart WHERE user_id = '{$row['user_id']}'";
                $cart_result = mysqli_query($con, $cart_check_sql);

                if (mysqli_num_rows($cart_result) === 0) {
                    $cart_insert_sql = "INSERT INTO projtest.cart (user_id) VALUES ('{$row['user_id']}')";
                    if (!mysqli_query($con, $cart_insert_sql)) {
                        throw new Exception("Failed to create a cart for the user.");
                    }
                }

                // Commit the transaction
                mysqli_commit($con);

                // Redirect back to the originating page
                $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index3.php';
                header("Location: $redirect");
                exit();
            } else {
                throw new Exception("Incorrect password. Please try again.");
            }
        } else {
            throw new Exception("No user found with this email.");
        }
    } catch (Exception $e) {
        // Rollback the transaction if something goes wrong
        mysqli_rollback($con);
        $loginError = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Boss Buy</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            flex-direction: column; /* Allow vertical alignment */
        }

        .login-container {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 350px;
            margin-bottom: 20px; /* Add space between form and footer */
        }

        h2 {
            margin-bottom: 20px;
            text-align: center;
            font-size: 24px;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }

        input {
            width: 100%;
            padding: 12px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        input:focus {
            border-color: #007bff;
            outline: none;
        }

        .btn {
            background: #007bff;
            color: #fff;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            transition: background 0.3s ease;
        }

        .btn:hover {
            background: #0056b3;
        }

        .footer {
            text-align: center;
            font-size: 14px;
            color: #555;
        }

        .footer a {
            color: #007bff;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .back-to-shop>a {
            text-decoration: none;
        }

        .back-to-shop > a:hover {
            font-size: 20px;
            transition: all 0.5s ease; 
        }

        .back-to-shop > a {
            transition: all 0.5s ease;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>

         <!-- Display error message if login fails -->
       <?php
        if (!empty($loginError)) {
            echo '<div class="error">' . $loginError . '</div>';
        }
        ?>
    </div>
    <div class="back-to-shop"><a href="index3.php">&leftarrow;</a><span class="text-muted">Back to shop</span></div>

    <div class="footer">
        Don't have an account? <a href="register.php">Register</a>
    </div>
</body>
</html>
