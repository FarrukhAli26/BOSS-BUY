<?php
session_start();

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


// Fetch the total amount in the cart
// $result = $con->query("SELECT SUM(price) AS total FROM projtest.cart_item");
// $total = 0;
// if ($result) {
//     $row = $result->fetch_assoc();
//     $total = $row['total'] ? $row['total'] : 0; // Default to 0 if no items in the cart
// }


$total = isset($_SESSION['total']) ? $_SESSION['total'] : 0;
$name = array();
$price = array();
$quantity = array();
$ser = array();

// Query the database to get all items
$query = "SELECT product_name, price, product_id FROM projtest.product WHERE category_id=2";
$result = $con->query($query);
$image_name= array();

if ($result && $result->num_rows > 0) {
    $i = 0;
    while ($row = $result->fetch_assoc()) {
        $name[$i] = $row['product_name']; // Correct the key for product_name
        $price[$i] = $row['price']; // Correct the key for price
        $quantity[$i] = 1; // Assuming a default quantity of 1 for now, update if necessary
        $image_name[$i] = strtoupper(str_replace(" ", "", $name[$i])).".avif";
        $i++; 
    }
} else {
    echo "failed: " . $con->error;
}

$div_contents = [];
for ($i = 0; $i < count($name); $i++) {
    // $image_name = strtoupper(str_replace(" ", "", $name[$i])) . ".avif";
      // echo "Generated Image Path: " . "women/" . $image_name[$i] . "<br>"; 
    $div_contents[] = '
    <div class="col-md-3">
    <a href="'.$name[$i].'.php"> <img src="women/'.$name[$i].'.avif"/></a>
    <div class="product-title"><b>'.$name[$i].'</b></div>
    <div class="price"><b>$'.$price[$i].'</b></div>
    </div>';
}


$div_contents_section_2 = [];
for ($i = 4; $i < count($name); $i++) {
    // Dynamically create content for section 2 (maybe different products or a different logic)
    $div_contents_section_2[] = '
        <div class="col-md-3">
        <a href="'.$name[$i].'.php"> <img src="women/'.$name[$i].'.avif"/></a>
        <div class="product-title"><b>'.$name[$i].'</b></div>
        <div class="price"><b>$'.$price[$i].'</b></div>
        </div>';
}



    // Close the database connection
$con->close();
// }
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="preconnect" href="//logx.optimizely.com" crossorigin="">
    <script src="https://kit.fontawesome.com/09b894bea4.js" crossorigin="anonymous"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
    <title>Product Interface</title>

    <style type="text/css">
         .sear {
        transition: opacity 0.5s ease;
    }

    .col-md-3{
        padding: 0px;
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

    .navbar-nav > li > a:link {
        color: black;
    }

    .login:hover {
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

    img{
        width: -webkit-fill-available !important;
    }

    .product-title, .price{
        margin: 5px 0px 0px 15px;
    }

    a > button{
         transition: all 0.3s ease;
    }
    a> button:hover{
        background-color: black !important;
        color: white !important;
        transition: all 0.3s ease;
        font-size: 17px !important;
    }

    </style>


</head>
<body>
    <div style=" border-bottom: 1px solid grey">
    <div class="container" style="padding: 0px;">
    <div class="row">
        <div class="col-md-12">
            <nav class="navbar navbar-default">
                <div class="container-fluid" style="background-color: white; padding-left: 0px">
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <ul class="nav navbar-nav">
                
                            <li class="logo"><a href="index3.php" style="padding: 12px 4px 0px 15px;"><img id="logo-img" src="images/chess2.png" height="3%"> <span class="sr-only">(current)</span></a></li>
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
</div>

<div style="margin: 53px 0px 20px 66px;"><h1 ><b style="border: 1px solid black; border-radius: 5px; padding: 2px 7px 2px 7px;">WOMEN'S ACCESSOIRES</b></h1></div>

<br>
<div class="container-fluid" style="display: flex; padding: 0px; margin-top: 35px">
    <div class="row" style="width: 7%; margin: 0px 0px 0px 64px">
        <a href="w_viewall.php"><button style="background-color: transparent; border: 1px solid black; color: black; font-size: 15px;">CLOTHES</button></a>
    </div>
    <div class="row" style="width:">
        <a href="w_accessories.php"><button class="accessories" style="background-color: transparent; border: 1px solid black; color: black; font-size: 15px; margin-left: 25px">ACCESSORIES</button></a>
    </div>
</div>

<br><br>

<div class="container-fluid">
    <div class="row" style="display: flex;">
        <!-- <div class="col-md-3">
            <a href="bow.php"><img src="women/bow.avif"></a>
            <div class="product-title"><b>HAIR CLIP WITH BOW</b></div>
            <div class="price"><b>$8.99</b></div>
        </div>
        <div class="col-md-3">
            <a href="earrings.php"><img src="women/earrings.avif"></a>
            <div class="product-title"><b>CHUNKY DOME EARRINGS</b></div>
            <div class="price"><b>$8.99</b></div>
        </div>
        <div class="col-md-3">
            <a href="boots.php"><img src="women/boots.avif"></a>
            <div class="product-title"><b>CHLSEA BOOTS</b></div>
            <div class="price"><b>$34.99</b></div>
        </div>
        <div class="col-md-3">
            <a href="bag.php"><img src="women/bag.avif"></a>
            <div class="product-title"><b>SHOULDER BAG</b></div>
            <div class="price"><b>$34.99</b></div>
        </div> -->
         <?php
                    // Output the dynamically generated HTML content
        foreach ($div_contents as $content) {
            echo $content;
        }
        ?>
        
    </div>
    <div class="row" style="display: flex; margin-top: 44px;">
        <!-- <div class="col-md-3">
            <a href="pumps.php"><img src="women/pumps.avif"></a>
            <div class="product-title"><b>RHINESTONE EMBELLISHED MESH PUMPS</b></div>
            <div class="price"><b>$49.99</b></div>
        </div> -->

        <?php
             // Output the dynamically generated HTML content
            foreach ($div_contents_section_2 as $content) {
                echo $content;
            }
        ?>
    </div>
</div>

<br><br><br><br>






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



</body>
</html>