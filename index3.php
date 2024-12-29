<?php
session_start(); // Start session

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

    // Collect post variables
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['name'])) {
       $name = mysqli_real_escape_string($con, $_POST['name']);
       $email = mysqli_real_escape_string($con, $_POST['email']);
       $msg = mysqli_real_escape_string($con, $_POST['msg']);
            // Corrected SQL syntax - remove quotes around table/column names
       $sql = "INSERT INTO projtest.form (name, email, other) VALUES ('$name','$email','$msg');";
            // $ser++;
            // echo $sql . "<br>";

            // Execute the query
       if($con->query($sql) === true){
                // Flag for successful insertion
        $insert = true;
    } else {
        echo "ERROR: $sql <br> $con->error";
    }
}

    // Check if 'item_name' is set to handle the "Add" button submission
// if (isset($_POST['item_name'])) {
//     $item_name = mysqli_real_escape_string($con, $_POST['item_name']);
//     $price = mysqli_real_escape_string($con, $_POST['price']);
//             // SQL query to insert the item into the database
//     $sql = "INSERT INTO projtest.cart (name, price) VALUES ('$item_name','$price');";

//             // Execute the query
//     if ($con->query($sql) === true) {
//         echo "Item added successfully!";
//     } else {
//         echo "ERROR: $sql <br> $con->error";
//     }
// }
}


$pricee = array();
$quantity = array();
$f_price = array();  // quantity*price;
// Fetch the total amount in the cart
$query = "SELECT 
ci.cart_item_id, 
ci.quantity, 
ci.product_id, 
p.product_name, 
p.price 
FROM projtest.cart_item ci
JOIN projtest.product p ON ci.product_id = p.product_id";

$result = $con->query($query);
$total = 0;

if ($result && $result->num_rows > 0) {
    $i = 0;
    while ($row = $result->fetch_assoc()) {
        $pricee[$i] = $row['price'];        // Assign price from the result
        $quantity[$i] = $row['quantity']; 
        $f_price[$i] = $pricee[$i] * $quantity[$i]; // Final price for this cart item
        $total=$total+$f_price[$i];
        $i++;
    }
} else {
    // echo "No items found in cart or query failed: " . $con->error;
}
$_SESSION['total']=$total;

$trigger_sql = "
CREATE TRIGGER update_product_stock_after_order
AFTER INSERT ON projtest.order_item
FOR EACH ROW
BEGIN
    -- Update the product stock by subtracting the quantity of the ordered item
    UPDATE projtest.product
    SET stock_quantity = stock_quantity - NEW.quantity
    WHERE product_id = NEW.product_id;
END;
";

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['Address'])) {
    // Ensure the user is logged in (check for session or user ID)
    if (!isset($_SESSION['user_id'])) {
        echo "You must be logged in to place an order.";
        exit;
    }

    // Get the user input
    $user_id = $_SESSION['user_id']; // Logged-in user
    $address = mysqli_real_escape_string($con, $_POST['Address']); // User address
    $total = $_SESSION['total']; // Total price stored in session
    $currentDate = date('Y-m-d'); // Current date in YYYY-MM-DD format

    // 1. Insert the order into the orders table
    $order_sql = "INSERT INTO projtest.orders (user_id, order_date, bill, address) 
    VALUES ('$user_id', '$currentDate', '$total', '$address')";

    if ($con->query($order_sql) === TRUE) {
        // Get the inserted order ID
        $order_id = $con->insert_id;

        // 2. Insert items from the cart into the order_item table
        // Fetch all items from the cart of the user
        $cart_sql = "
        SELECT ci.cart_item_id, ci.product_id, ci.quantity, p.price
        FROM projtest.cart_item ci
        JOIN projtest.product p ON ci.product_id = p.product_id
        WHERE ci.cart_id = (SELECT cart_id FROM projtest.cart WHERE user_id = '$user_id')
        ";

        $result_cart = $con->query($cart_sql);
        if ($result_cart->num_rows > 0) {
            while ($row = $result_cart->fetch_assoc()) {
                $product_id = $row['product_id'];
                $quantity = $row['quantity'];
                $price = $row['price'];

                // Insert each cart item into the order_items table
                $order_item_sql = "INSERT INTO projtest.order_item (product_id, order_id, quantity, price)
                VALUES ('$product_id', '$order_id', '$quantity', '$price')";

                if ($con->query($order_item_sql) === FALSE) {
                    echo "Error: " . $con->error;
                }
            }


            // Optionally, delete the cart items after placing the order
            $delete_cart_items = "DELETE FROM projtest.cart_item WHERE cart_id = (SELECT cart_id FROM projtest.cart WHERE user_id = '$user_id')";
            if ($con->query($delete_cart_items) === TRUE) {
                // Cart items deleted successfully
            } else {
                echo "Error deleting cart items: " . $con->error;
            }

            // Redirect to the homepage after successful order placement
            header("Location: index3.php?order_success=true");
            exit; // Make sure to stop further code execution after redirect
        } else {
            echo "No items found in your cart.";
        }
    } else {
        echo "Error: " . $con->error;
    }
}





    // Close the database connection
$con->close();
// }
?>


<!doctype html>
<html lang="en">
<head>
  <script src="https://kit.fontawesome.com/09b894bea4.js" crossorigin="anonymous"></script>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Boss-Buy</title>
  <link rel="shortcut icon" href="images/bossbuy.png" type="image/x-icon">
  <!-- Bootstrap -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
<!--   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
  <link rel="stylesheet" type="text/css" href=""> -->

<!-- #684200  golden

#6e0707 red
#00114c blue
#1c1c1c black 2-->

<style type="text/css">

    .add_button {
        color: #ffa201;
        background-color: black;
        border: 1px solid black; 
        padding: 10px 20px; 
        font-weight: 700 !important;
        cursor: pointer; 
        border-radius: 5px;
        transition: all 0.5s ease;
    }

    .add_button:hover {
        background-color: transparent ;
        border: 1px solid #ffa201; 
        color: black !important;
    }
    .container {
        padding: 20px 0px;
    }

    .navbar-nav > li > a:hover {
        /* Uncomment if you want a hover effect for navbar items */
        /* background-color: black !important; */
        /* color: white !important; */
    }

    .sear {
        transition: opacity 0.5s ease;
    }

    .sear:hover {
        background-color: #cf0505 !important;  /* Remove the extra colon */
    }

    #logo-img {
        transition: opacity 0.5s ease;
    }

    #logo-img.fade-out {
        opacity: 0;
    }

    #logo-img.fade-in {
        opacity: 1;
    }

    .navbar-nav > li > a:link {
        color: black;
    }

   /* .login a:hover {
        color: #cf0505 !important;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .login > span:hover {
        color: #cf0505 !important;
        text-decoration: none;
        transition: all 0.3s ease;
        }*/

        .navbar-default {
            border-left: 5px solid black;
            margin: 0px;
            border-right: none !important;
            border-bottom: none !important;
            border-top: none !important;
        }

        .navbar-collapse {
            padding-left: 0px;
        }

        .input-group > span:hover {
            background-color: #cf0505;
            color: white;
            transition: all ease 0.4s;
        }

        .navbar-nav .dropdown-menu {
            visibility: hidden;
            opacity: 0;
            transition: visibility 0s, opacity 0.5s ease-in-out;
        }

        .navbar-nav .dropdown:hover > .dropdown-menu {
            visibility: visible;
            opacity: 1;
            background-color: #f0f0f0;
        }

        .lining {
            width: 99%;
            padding: 0px;
        }

        .carousel-inner {
            width: 100% !important;
        }

        .line1, .line2, .line3, .line4 {
            border-bottom: 2px solid #dddddd;
            margin: 36px 0px 0px 0px;
            padding: 0px;
            height: 0px;
            color: white;
        }

        .line1 {
            width: 78%;
        }

        .line2 {
            width: 59%;
        }

        .line3 {
            width: 51%;
        }

        .line4 {
            width: 56%;
        }

        .producttitle, .refresh {
            font-size: 24px;
            font-weight: 700;
            color: #595959;
            text-transform: uppercase;
            text-align: left;
        }

        .fa-rotate-right, .fa-user, .fa-refresh {
            color: #cacaca !important;
        }

        .last {
            margin-top: 85px;
            background-color: #ffffff;
        }

        .bottom {
            margin-left: 201px;
        }

        /* Button hover effect */


        .showmore {
            transition: all 0.5s ease;
        }

        .showmore:hover {
            background-color: white !important;
            color: black;
            border: 1px solid #1ba0da !important;
            transition: all 0.5s ease;
        }

        .logos, .l {
            transition: all 0.5s ease;
        }

        .l:hover, .l2:hover, .l3:hover {
            color: black !important;
            transition: all 0.3s ease;
        }

        .logos:hover {
            background-color: white !important;
            transition: all 0.3s ease;
        }

        .faq > a {
            transition: all 0.5s ease;
        }

        .faq > a:hover {
            font-size: 20px;
            transition: all 0.5s ease;
        }

        .btn-primary {
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: white !important;
            color: black;
            transition: all 0.5s ease;
        }

        .productinfo > li > a {
            border-bottom-width: 0% !important;
            transition: all 0.5s ease;
        }

        .productinfo > li > a:hover {
            color: black !important;
            font-size: 17px;
            transition: all 0.5s ease;
        }
        .container {
            padding: 20px 0px;
        }

        .navbar-nav > li > a:hover {
            /* Uncomment if you want a hover effect for navbar items */
            /* background-color: black !important; */
            /* color: white !important; */
        }

        .sear {
            transition: opacity 0.5s ease;
        }

        .sear:hover {
            background-color: #cf0505 !important;  /* Remove the extra colon */
        }

        #logo-img {
            transition: opacity 0.5s ease;
        }

        #logo-img.fade-out {
            opacity: 0;
        }

        #logo-img.fade-in {
            opacity: 1;
        }

        .navbar-nav > li > a:link {
            color: black;
        }

        .login:hover {
            color: #cf0505 !important;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .logout{
             transition: all 0.3s ease;
        }
        .logout:hover {
            color: #cf0505 !important;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .glyphicon:hover {
            color: #cf0505 !important;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .glyphicon > a:hover {
            color: #cf0505 !important;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .login{
            transition: all 0.3s ease;
        }

        .glyphicon{
            transition: all 0.3s ease;
        }

        .navbar-default {
            border-left: 5px solid black;
            margin: 0px;
            border-right: none !important;
            border-bottom: none !important;
            border-top: none !important;
        }

        .navbar-collapse {
            padding-left: 0px;
        }

        .input-group > span:hover {
            background-color: #cf0505;
            color: white;
            transition: all ease 0.4s;
        }

        .navbar-nav .dropdown-menu {
            visibility: hidden;
            opacity: 0;
            transition: visibility 0s, opacity 0.5s ease-in-out;
        }

        .navbar-nav .dropdown:hover > .dropdown-menu {
            visibility: visible;
            opacity: 1;
            background-color: #f0f0f0;
        }

        .lining {
            width: 99%;
            padding: 0px;
        }

        .carousel-inner {
            width: 100% !important;
        }

        .line1, .line2, .line3, .line4 {
            border-bottom: 2px solid #dddddd;
            margin: 36px 0px 0px 0px;
            padding: 0px;
            height: 0px;
            color: white;
        }

        .line1 {
            width: 78%;
        }

        .line2 {
            width: 59%;
        }

        .line3 {
            width: 51%;
        }

        .line4 {
            width: 56%;
        }

        .producttitle, .refresh {
            font-size: 24px;
            font-weight: 700;
            color: #595959;
            text-transform: uppercase;
            text-align: left;
        }

        .fa-rotate-right, .fa-user, .fa-refresh {
            color: #cacaca !important;
        }

        .last {
            margin-top: 85px;
            background-color: #ffffff;
        }

        .bottom {
            margin-left: 201px;
        }

        /* Button hover effect */
        .addd_buttton {
            border: 1px solid black; 
            padding: 10px 20px; 
            font-weight: 700 !important;
            cursor: pointer; 
            border-radius: 5px;
            transition: all 0.5s ease;
        }

        .addd_buttton:hover {
            background-color: #684200 !important;
            border: 2px solid black; 
            color: black !important;
        }

        .showmore {
            transition: all 0.5s ease;
        }

        .showmore:hover {
            background-color: white !important;
            color: black;
            border: 1px solid #1ba0da !important;
            transition: all 0.5s ease;
        }

        .logos, .l {
            transition: all 0.5s ease;
        }

        .l:hover, .l2:hover, .l3:hover {
            color: black !important;
            transition: all 0.3s ease;
        }

        .logos:hover {
            background-color: white !important;
            transition: all 0.3s ease;
        }

        .faq > a {
            transition: all 0.5s ease;
        }

        .faq > a:hover {
            font-size: 20px;
            transition: all 0.5s ease;
        }

        .btn-primary {
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: white !important;
            color: black;
            transition: all 0.5s ease;
        }

        .productinfo > li > a {
            border-bottom-width: 0% !important;
            transition: all 0.5s ease;
        }

        .productinfo > li > a:hover {
            color: black !important;
            font-size: 17px;
            transition: all 0.5s ease;
        }

    </style>


</head>
<body>
  <div class="container">
    <div class="row">
      <div class="col-md-4 animate__animated animate__swing">
        <img src="images/bbcrop.png" style="height: 63.14px; width: 192px; border: 0;">
    </div>
    <div class="col-md-4" style="padding: 30px 15px 0px 30px; margin-left: 250px;">
        <div class="input-group" style="width: 90%">
          <input type="text" class="form-control" placeholder="Search products" aria-describedby="basic-addon2">
          <span class="input-group-addon sear" id="basic-addon2" style="padding: 0px 5px 0px 5px;">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
        </span>
    </div>
</div>
<!-- <div class="col-md-4 login" style="padding: 37px 0px 0px 0px; width: 11%; margin-left: -30px;">
    <a href="#" style="padding-right: 10px; color: #919191">Login</a>
    <span class="glyphicon glyphicon-shopping-cart" aria-hidden="true">
    <a href="cart.php" style="color: #919191">[<?php echo '$' . number_format($total, 2); ?>]</a>
</span>

</div> -->
</div>
</div>
<hr style="margin: 0px;">
<div class="container" style="padding: 0px">
    <div class="row">
        <div class="col-md-12">
            <nav class="navbar navbar-default">
                <div class="container-fluid" style="background-color: white; padding-left: 0px">
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <ul class="nav navbar-nav">

                            <li class="logo"><a href="#" style="padding: 12px 4px 0px 15px;"><img id="logo-img" src="images/chess2.png" height="3%"> <span class="sr-only">(current)</span></a></li>
                            <li><a href="#">About us</a></li>
                            <li><a href="m_viewall.php">MEN</a></li>
                            <li><a href="w_viewall.php">WOMEN</a></li>
                            <li style="display: flex; align-items: center; gap: 15px;">
                                <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])): ?>
                                 <a href="my_orders.php" style="padding-right: 10px; color: #919191">My Orders</a>
                                    <!-- Show Logout -->
                                    <a href="javascript:void(0);" 
                                    class="logout" 
                                    style="padding-right: 10px; color: #919191" 
                                    onclick="confirmLogout('logout.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>')">
                                    Logout
                                </a>
                                <?php else: ?>
                                    <!-- Show Login -->
                                    <a href="login.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" 
                                       class="login" 
                                       style="padding-right: 10px; color: #919191">Login</a>
                               <?php endif; ?>
                           </li>


                           <li>
                            <span class="glyphicon glyphicon-shopping-cart" aria-hidden="true" style="margin-top: 17px">
                                <a href="bbcart.php" style="color: #919191">[<?php echo '$' . number_format($total, 2); ?>]</a>
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</div>
</div>



<div class="container-fluid" style="padding: 0px">
    <div class="row" style="display: flex;">
        <div class="col-md-6" style="padding: 0px;">
            <img src="images/men.avif" style="width: -webkit-fill-available">
        </div>
        <div class="col-md-6" style="padding: 0px;">
            <img src="images/women.avif" style="width: -webkit-fill-available">
        </div>
    </div>
</div>


<div class="container-fluid" style="padding: 0px">
    <h2><b>MEN</b></h2>
    <div class="row" style="display: flex;">
        <div class="col-md-4" style="padding: 0px">
            <a href="REGULAR-FIT SHIRT WITH SCARF COLLAR.php"> <img src="men/REGULAR-FIT SHIRT WITH SCARF COLLAR1.avif" style="width: -webkit-fill-available"></a>
        </div>
        <div class="col-md-4" style="padding: 0px">
         <a href="REGULAR FIT JERSEY BOMBER JACKET.php"> <img src="men/REGULAR FIT JERSEY BOMBER JACKET1.avif" style="width: -webkit-fill-available"></a>
     </div>
     <div class="col-md-4" style="padding: 0px">
         <a href="SLIM FIT SHIMMERY MOCK TURTLENECK SWEATER.php"> <img src="men/SLIM FIT SHIMMERY MOCK TURTLENECK SWEATER1.avif" style="width: -webkit-fill-available"></a>
     </div>
 </div>
 <h2><b>WOMEN</b></h2>
 <div class="row" style="display: flex;">
    <div class="col-md-4" style="padding: 0px">
        <a href="FLARED-SKIRT TWILL DRESS.php"><img src="women/FLARED-SKIRT TWILL DRESS1.avif" style="width: -webkit-fill-available"></a>
    </div>
    <div class="col-md-4" style="padding: 0px">
        <a href="TUXEDO BLAZER.php"><img src="women/TUXEDO BLAZER1.avif" style="width: -webkit-fill-available"></a>
    </div>
    <div class="col-md-4" style="padding: 0px">
     <a href="JACQUARD-WEAVE MINI DRESS.php"> <img src="women/JACQUARD-WEAVE MINI DRESS1.avif" style="width: -webkit-fill-available"></a>
 </div>
</div>
</div>


<br><br><br>

<div class="container-fluid"  style="background-color: #fbfbfb; padding: 20px 0px 54px 0px;">
    <div class="container">
      <div class="row">
        <div class="col-md-4">
          <div class="col-md-2" style="width: 30%">
            <i class="fa-solid fa-rotate-right fa-5x"></i>
        </div>
        <div class="col-md-2" style="width: 70%">
            <h5 class="refresh">DELIVERY PROCESS</h5>
            <p style="line-height: 18px; font-size: 12px">Free shipping on orders over $75</p>
        </div>
    </div>
    <div class="col-md-4">
      <div class="col-md-2" style="width: 30%">
        <i class="fa fa-user fa-5x"></i>
    </div>
    <div class="col-md-2" style="width: 70%">
        <h5 class="refresh">CUSTOMER CARE</h5>
        <p style="line-height: 18px; font-size: 12px">BossBuyLA@gmail.com</p>
    </div>
</div>
<div class="col-md-4">
  <div class="col-md-2" style="width: 30%">
    <i class="fa fa-refresh fa-5x"></i>
</div>
<div class="col-md-2" style="width: 70%">
    <h5 class="refresh">RETURN POLICY</h5>
    <p class="faq" style="line-height: 18px; font-size: 12px">Refer to our <a href="C:\Users\src\Desktop\website\site\bootsrap\FAQ.html" style="text-decoration: none; color: #c97178">FAQ</a> Page</p>
</div>
</div>
</div>
</div>
</div>

<div style="height: 5px; background-color: #e7e7e7"> </div>


<div class="container-fluid last">
    <div class="row bottom">
      <div class="col-md-3" style=" width: 23%; margin-top: 17px;">
        <div class="container lining" style="display: flex;">
          <h4 style="margin-left: 0px; margin-bottom: 0px; border-bottom: 2px solid #cf0505; width: 21%; padding: 0px 0px 10px 0px; font-size: 15px; font-weight: 700; height: 27px">ABOUT</h4>
          <h3 class="line1"></h3>
      </div>
      <!-- <hr style="margin-left: 0px; width: 12%; border-top: 3px solid #cf0505; margin-bottom: 0px;"> <hr style="margin-left: 389px; margin-top: 0px; border-top: 1px solid #977f7f; width: 60%"> -->
      <br>
      <img src="images/bbcrop.png" style="width: 218px; height: 86px;">
      <p style="font-size: 100%; font-family: inherit; font-weight: inherit">Our story began in Chicago, IL in 1999— when Numan Karim, a young and determined entrepreneur Realized that he had something special to offer the beauty and fragrance  industry. He knew that this market needed top-of-the line products made with that amazing combination of high quality, simple and natural ingredients, all topped off with love.</p>
  </div>
  <div class="col-md-3" style=" width: 23%;">
    <div class="container lining" style="display: flex;">
      <h4 style="margin-left: 0px; margin-bottom: 0px; border-bottom: 2px solid #cf0505; width: 79%; padding: 0px 0px 0px 0px; font-size: 15px; font-weight: 700; margin-top: 27px">PRODUCT INFO</h4><h4 class="line2" style="margin-top: 53px;">.</h4>
      <!-- <hr style="margin-left: 0px; width: 12%; border-top: 3px solid #cf0505; margin-bottom: 0px;"> <hr style="margin-left: 389px; margin-top: 0px; border-top: 1px solid #977f7f; width: 60%"> -->
  </div>
  <br>
  <ul class="productinfo">
      <li> <a href="#" style="text-decoration: none; color: #c97178">Accessories</a></li>
      <li> <a href="#" style="text-decoration: none; color: #c97178">Clothes</a></li>
  </ul>
</div>
<div class="col-md-3" style=" width: 23%;">
    <div class="container lining" style="display: flex;">
      <h4 style="margin-left: 0px; margin-bottom: 0px; border-bottom: 2px solid #cf0505; width: 111%; padding: 0px 0px 0px 0px; font-size: 15px; font-weight: 700; margin-top: 27px;">SUPPORT CENTER</h4><h4 class="line3" style="margin-top: 53px;">.</h4>
      <!-- <hr style="margin-left: 0px; width: 12%; border-top: 3px solid #cf0505; margin-bottom: 0px;"> <hr style="margin-left: 389px; margin-top: 0px; border-top: 1px solid #977f7f; width: 60%"> -->
  </div>
  <br>
  <p style="font-weight: 600; text-align: left; line-height: 23px">BOSS BUY INC<br>
      LA, Clifornia<br> 
      Tel: 7089496457
  </p>
  <div class="logos" style="background-color: #1ba0da; width: 9%;padding: 1px 0px 0px 5px;; border-radius: 4px; height: 23px; float: left; margin-right: 15px;">
      <i class="fa-solid fa-envelope l" style="color: white;"></i>
  </div>
  <div class="logos" style="background-color: #1ba0da; width: 9%;padding: 1px 0px 0px 5px;; border-radius: 4px; height: 23px; float: left; margin-right: 15px;">
      <i class="fa-brands fa-instagram l2" style="color: white; font-size: 19px; padding-top: 1px;"></i>
  </div>
  <div class="logos" style="background-color: #1ba0da; width: 9%;padding: 1px 0px 0px 5px;; border-radius: 4px; height: 23px; float: left;">
      <i class="fa-brands fa-facebook-f l3" style="color: white;  font-size: 22px; padding-left: 5px;"></i>
  </div>
</div>
<div class="col-md-3" style=" width: 23%;">
    <div class="container lining" style="display: flex;">
      <h4 style="margin-left: 0px; margin-bottom: 0px; border-bottom: 2px solid #cf0505; width: 86%; padding: 0px 0px 0px 0px; font-size: 15px; font-weight: 700; margin-top: 27px">QUICK CONTACT</h4><h4 class="line4" style="margin-top: 53px;">.</h4>
      <!-- <hr style="margin-left: 0px; width: 12%; border-top: 3px solid #cf0505; margin-bottom: 0px;"> <hr style="margin-left: 389px; margin-top: 0px; border-top: 1px solid #977f7f; width: 60%"> -->
  </div>
  <br>
  <?php
  if($insert == true){
    echo "<p class='submitMsg'>Thanks for your review !</p>";

}
?>
<div class="form">
  <form action="index3.php" method="post">
    <div class="finame">
      <input type="text" id="name" name="name" placeholder="Enter Your Name" required style="height: 35px; border: 1px solid #d4d4d4; padding: 0px 0px 0px 10px; width: 100%"><br>
  </div>
  <br>
  <div class="laname">
      <input type="text" id="email" name="email" placeholder="Enter Your Email" required style="height: 35px; border: 1px solid #d4d4d4; padding: 0px 0px 0px 10px; width: 100%"><br>
  </div>
  <br>
  <div>
      <textarea id="msg" name="msg" placeholder="Enter Your Message" style="width: 100%; padding: 5px 0px 0px 10px; height: 150px; border: 1px solid #d4d4d4"></textarea>
  </div>
  <br>
  <button type="submit" class="btn btn-primary" style="background-color: #1ba0da; border-radius: 0px; border: 2px solid; line-height: 28px; font-size: 16px; padding: 5px 20px; font-weight: 600; font-family: inherit; border-top: 2px solid #545454; border-left: 2px solid #545454; border-bottom: 2px solid black; border-right: 2px solid black; cursor: pointer; ">Submit
  </button>
</form>
</div>
</div>
</div>
</div>
<br><br><br>




<script>
    const logoImg = document.getElementById('logo-img');

    // Function to change image on hover
    function changeImage(newSrc) {
        logoImg.classList.add('fade-out');  // Start fade-out
        setTimeout(function() {
            logoImg.src = newSrc;            // Change image source after fade-out
            logoImg.classList.remove('fade-out'); // Remove fade-out class
            logoImg.classList.add('fade-in'); // Start fade-in
        }, 500);  // Wait for the fade-out to complete
    }

    // Function to reset the image on mouseout
    function resetImage(originalSrc) {
        logoImg.classList.add('fade-out');  // Start fade-out
        setTimeout(function() {
            logoImg.src = originalSrc;       // Reset image source after fade-out
            logoImg.classList.remove('fade-out'); // Remove fade-out class
            logoImg.classList.add('fade-in'); // Start fade-in
        }, 500);  // Wait for the fade-out to complete
    }

    // Event listeners for hover and mouse out
    logoImg.addEventListener('mouseover', function() {
        changeImage('images/chess.png');
    });

    logoImg.addEventListener('mouseout', function() {
        resetImage('images/chess2.png');
    });

    // Remove the 'fade-in' class after the transition completes so it can be reapplied
    logoImg.addEventListener('transitionend', function() {
        logoImg.classList.remove('fade-in');
    });



     function confirmLogout(logoutUrl) {
        // Show a confirmation dialog
        const confirmAction = confirm("Are you sure you want to logout?");
        if (confirmAction) {
            // Redirect to the logout URL if confirmed
            window.location.href = logoutUrl;
        } else {
            // Do nothing if the user cancels
            console.log("Logout cancelled by the user.");
        }
    }
</script>



<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha384-nvAa0+6Qg9clwYCGGPpDQLVpLNn0fRaROjHqs13t4Ggj3Ez50XnGQqc/r8MhnRDZ" crossorigin="anonymous"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>
</body>
</html>
