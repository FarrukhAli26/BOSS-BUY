<?php
$insert = false;
// if(isset($_POST['name'])){ // Uncomment this if you want to check for POST submission
    // Set connection variables
$server = "localhost";
$username = "root";
$password = "";

    // Create a database connection
$con = mysqli_connect($server, $username, $password);

    // Check for connection success
if(!$con){
    die("connection to this database failed due to " . mysqli_connect_error());
}
echo "Success connecting to the db<br>";



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get values from the form
    $f_name = $con->real_escape_string($_POST['firstname']);
    $l_name = $con->real_escape_string($_POST['lastname']);
    $email = $con->real_escape_string($_POST['email']);
    $password = password_hash($con->real_escape_string($_POST['password']), PASSWORD_BCRYPT); // Hash the password
    $address = $con->real_escape_string($_POST['address']);
    $city = $con->real_escape_string($_POST['city']);
    $state = $con->real_escape_string($_POST['state']);
    $postal_code = $con->real_escape_string($_POST['postalcode']);
    $country = $con->real_escape_string($_POST['country']);
    $mobile = $con->real_escape_string($_POST['mobile']);

    // Prepare the SQL statement
    $sql = "INSERT INTO projtest.user (f_name, l_name, mobile, city, country, address, state_province, email, password) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepare and bind parameters
    $stmt = $con->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("sssssssss", $f_name, $l_name,$mobile ,$city ,$country, $address, $state,$email,$password );

        // Execute the statement
        if ($stmt->execute()) {
            echo "Registration successful!";
            header("Location: login.php"); // Redirect to a success page
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    }
     else {
        echo "Error preparing the statement: " . $con->error;
    }

    // Close the database connection
    $con->close();
} else {
    echo "Invalid request method.";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Boss Buy</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #e9ebee;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            width: 90%;
            max-width: 600px;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            margin: 20px;
        }

        .container h2 {
            text-align: center;
            font-size: 28px;
            margin-bottom: 10px;
            color: #333;
        }

        .container p {
            text-align: center;
            color: #777;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .form-group input::placeholder {
            color: #aaa;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #1877f2;
            box-shadow: 0 0 3px rgba(24, 119, 242, 0.5);
            outline: none;
        }

        .form-group select {
            color: #aaa; /* Grey color for select dropdown text */
        }

        .form-group select option {
            color: #aaa; /* Grey color for dropdown options */
        }

        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .form-row .form-group {
            flex: 1;
        }

        .dob-container {
            display: flex;
            gap: 10px;
        }

        .dob-container select {
            flex: 1;
        }

        .btn {
            display: block;
            width: 100%;
            background-color: #1877f2;
            color: white;
            padding: 14px;
            font-size: 16px;
            text-align: center;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }

        .btn:hover {
            background-color: #155db8;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #555;
            margin-top: 10px;
        }

        .footer a {
            color: #1877f2;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
    <script>
        function validateForm() {
            const password = document.getElementById("password").value;
            const confirmPassword = document.getElementById("confirmpassword").value;
            
            if (password !== confirmPassword) {
                alert("Passwords do not match.");
                return false;
            }
            
            // Check for required fields
            const requiredFields = [
                "firstname", "lastname", "email", "password", 
                "confirmpassword", "address", "city", "state", "country"
            ];
            
            for (let field of requiredFields) {
                const value = document.getElementById(field).value.trim();
                if (value === "") {
                    alert(`${field.charAt(0).toUpperCase() + field.slice(1)} is required.`);
                    return false;
                }
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Sign Up</h2>
        <p>It's quick and easy.</p>
        <form action="register.php" method="POST" onsubmit="return validateForm()">
            <!-- Name Fields -->
            <div class="form-row">
                <div class="form-group">
                    <input type="text" id="firstname" name="firstname" placeholder="First Name (Required)" required>
                </div>
                <div class="form-group">
                    <input type="text" id="lastname" name="lastname" placeholder="Last Name (Required)" required>
                </div>
            </div>
            
            <!-- Email Field -->
            <div class="form-group">
                <input type="email" id="email" name="email" placeholder="Email (Required)" required>
            </div>
            
            <!-- Password Fields -->
            <div class="form-row">
                <div class="form-group">
                    <input type="password" id="password" name="password" placeholder="Password (Required)" required>
                </div>
                <div class="form-group">
                    <input type="password" id="confirmpassword" name="confirmpassword" placeholder="Confirm Password (Required)" required>
                </div>
            </div>
            
            <!-- Address Fields -->
            <div class="form-group">
                <input type="text" id="address" name="address" placeholder="Street Address (Required)" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <input type="text" id="city" name="city" placeholder="City (Required)" required>
                </div>
                <div class="form-group">
                    <input type="text" id="state" name="state" placeholder="State/Province (Required)" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <input type="text" id="postalcode" name="postalcode" placeholder="Postal Code">
                </div>
                <div class="form-group">
                    <input type="text" id="country" name="country" placeholder="Country (Required)" required>
                </div>
            </div>

            <!-- Mobile Field -->
            <div class="form-group">
                <input type="text" id="mobile" name="mobile" placeholder="Mobile Number">
            </div>
            
            <!-- Submit Button -->
            <button type="submit" class="btn">Register</button>
        </form>
        <div class="footer">
            Already have an account? <a href="login.html">Log In</a>
        </div>
    </div>
</body>
</html>
