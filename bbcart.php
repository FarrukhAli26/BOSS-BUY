<?php
session_start(); // Start the session to track the logged-in user

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You are not logged in.";
    exit; // Exit if not logged in
}

// Set connection variables
$server = "localhost";
$username = "root";
$password = "";
$dbname = "projtest"; // Database name

// Create a database connection
$con = mysqli_connect($server, $username, $password, $dbname);

// Check for connection success
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get session data
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$email = $_SESSION['email'];

// Initialize arrays for storing item names and prices
$total = 0;
$name = array();
$price = array();
$quantity = array();
$product_id = array();
$f_price = array();
$cart_item_id = array();
echo "string";

// Initialize $total_items to avoid undefined variable warning
$total_items = 0;

// Query to fetch all cart items and join with product data
$query = "SELECT 
    ci.cart_item_id, 
    ci.quantity, 
    ci.product_id, 
    p.product_name, 
    p.price 
FROM projtest.cart_item ci
JOIN projtest.product p ON ci.product_id = p.product_id
WHERE ci.cart_id = (SELECT cart_id FROM projtest.cart WHERE user_id = '$user_id')";

$result = $con->query($query);

// Query to count the total number of cart items
$query2 = "
    SELECT COUNT(cart_item_id) from projtest.cart_item
    WHERE cart_id = (SELECT cart_id FROM projtest.cart WHERE user_id = '$user_id')
";

$result2 = $con->query($query2);

if ($result2) {
    $total_items = $result2->fetch_row()[0];
} else {
    echo "Error: " . $con->error;
}


// Initialize session arrays to store quantities and product IDs
$_SESSION['quantity'] = array();
$_SESSION['product_id'] = array();

// Check if the query was successful and has results
if ($result && $result->num_rows > 0) {
    $i = 0;
    while ($row = $result->fetch_assoc()) {
        // Store cart item data
        $cart_item_id[$i] = $row['cart_item_id'];
        $quantity[$i] = $row['quantity'];
        $product_id[$i] = $row['product_id'];
        $name[$i] = $row['product_name'];
        $price[$i] = $row['price']; // Base price of the product
        $f_price[$i] = $price[$i] * $quantity[$i]; // Final price for this cart item

        // Add quantities and product IDs to session arrays
        $_SESSION['quantity'][$i] = $quantity[$i];
        $_SESSION['product_id'][$i] = $product_id[$i];

        // Update total price
        $total = $total + $f_price[$i];
        $i++;
    }
} else {
    echo "No items found in cart or query failed: " . $con->error;
}

$_SESSION['total'] = $total;


// echo "<pre>$trigger_sql</pre>"; // Display the trigger SQL in the page (for debugging purposes)

// Prepare content for cart display (only if cart items exist)
$div_contents = [];
if ($total_items > 0) {
    for ($i = 0; $i < $total_items; $i++) {
        $div_contents[] = "
        <div class='row main align-items-center'>
        <div class='row' style='display: flex;'>

        <!-- Product Name -->
        <div class='col-md-4' style='width: 58%;'>
        <div class='row'>" . htmlspecialchars($name[$i]) . "</div>
        </div>
        
        <!-- Quantity Controls -->
        <div class='col-md-4 summ' style='width: 32%;'>
        <a href='#' class='decrease-qty' data-item-id='" . htmlspecialchars($cart_item_id[$i]) . "'>-</a>
        <a href='#' class='border' id='item-qty-" . htmlspecialchars($cart_item_id[$i]) . "'>
        " . htmlspecialchars($quantity[$i]) . "
        </a>
        <a href='#' class='increase-qty' data-item-id='" . htmlspecialchars($cart_item_id[$i]) . "'>+</a>
        </div>
        
        <!-- Price and Remove -->
        <div class='col-md-4 pricee' style='width: 22%;'>&dollar; 
        " . number_format($f_price[$i], 2) . "
        <span class='close' data-item-id='" . htmlspecialchars($cart_item_id[$i]) . "'>
        <a href='#'>&times;</a>
        </span>
        </div>
        </div>
        </div>
        ";
    }
} else {
    echo "Your cart is empty.";
}

// Close the database connection
$con->close();
?>




    <!doctype html>
    <html lang="en">
    <head>

      <script src="https://kit.fontawesome.com/09b894bea4.js" crossorigin="anonymous"></script>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <style>
        body{
            background: #ddd;
            min-height: 100vh;
            vertical-align: middle;
            display: flex;
            font-family: sans-serif;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .card{
            margin: auto;
            max-width: 950px;
            width: 90%;
            box-shadow: 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            border-radius: 1rem;
            border: transparent;
        }
        .cart{
            background-color: #fff;
            padding: 4vh 5vh;
            border-bottom-left-radius: 1rem;
            border-top-left-radius: 1rem;
        }
        .summary{
            background-color: #ddd;
            border-top-right-radius: 1rem;
            border-bottom-right-radius: 1rem;
            padding: 4vh;
            color: rgb(65, 65, 65);
        }
        .summary .col-2{
            padding: 0;
        }
        .summary .col-10 {
            padding: 0;
        }
        .row{
            margin: 0;
        }
        .main{
            margin: 0;
            padding: 2vh 0;
            width: 100%;
        }
        .col-2, .col{
            padding: 0 1vh;
        }
        a{
            padding: 0 1vh;
            color: rgb(65, 65, 65);
            text-decoration: none;
        }
        .summ> a:hover{
            color: #cf0505;
            transition: all 0.5s ease ; 
        }
        .summ> a{
           transition: all 0.5s ease;
       }
       .pricee> span> a:hover{
        color: #cf0505;
        transition: all 0.5s ease ; 
    }
    .pricee> span> a{
       transition: all 0.5s ease;
   }

   .close{
    margin-left: auto;
    font-size: 0.7rem;
}
img{
    width: 3.5rem;
}
.back-to-shop{
    margin-top: 4.5rem;
}
.back-to-shop > a:hover{
    font-size: 20px;
    transition: all 0.5s ease ; 
}
.back-to-shop > a{
    transition: all 0.5s ease;
}

.btn{
    transition: all 0.5s ease;
}

.btn:hover{
    transition: all 0.5s ease;
    background-color: black;
    color: white;
    font-size: 17px;
}

</style>
</head>
<body>
    <div class="card">
        <div class="row" style="display: flex;">
            <div class="col-md-6 cart" style="width: 50%">
                <div class="title">
                    <div class="row">
                        <div class="col"><h4><b>Shopping Cart</b></h4></div>
                        <div class="col align-self-center text-right text-muted">ITEMS <?php echo number_format($total_items); ?></div>
                    </div>
                </div>
                <div class="row border-top border-bottom">
                    <?php
        // Output the dynamically generated HTML content
                    foreach ($div_contents as $content) {
                        echo $content;
                    }
                    ?>
                </div>
                <div class="back-to-shop"><a href="index3.php">&leftarrow;</a><span class="text-muted">Back to shop</span></div>
            </div>
            <div class="col-md-6 summary" style="width: 50%">
                <div><h5><b>Summary</b></h5></div>
                <hr>
                <div class="row">
                    <div class="col" style="padding-left:0;">ITEMS <?php echo number_format($total_items); ?></div>
                    <div class="col text-right">&dollar;<?php echo number_format($total, 2); ?></div>
                </div>
                <form action="index3.php" method="post" onsubmit="return validateform()">
                    <p>YOUR ADDRESS</p>
                    <textarea id="Address" name="Address" placeholder="Enter your Address" required></textarea>
                    <p>CASH ON DELIVERY ONLY</p>
                    <div class="row" style="border-top: 1px solid rgba(0,0,0,.1); padding: 2vh 0;">
                        <div class="col">TOTAL PRICE </div>
                        <div class="col text-right">&dollar;<?php echo number_format($total, 2); ?></div>
                    </div>
                    <button type="submit" class="btn"> PLACE ORDER </button>
                </form>

            </div>
        </div>
    </div>

    <script>
    // Increase the quantity of an item
    document.querySelectorAll('.increase-qty').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

        // Get the itemId from the data-item-id attribute
        const itemId = this.getAttribute('data-item-id');
        let qtyElement = document.getElementById('item-qty-' + itemId);
        let currentQty = parseInt(qtyElement.textContent);

        // Update quantity and send request to server
        let newQty = currentQty + 1;
        qtyElement.textContent = newQty; // Update front-end immediately
        
        // Send update to server

        updateQuantity(itemId, newQty);
    });
    });

// Decrease the quantity of an item
document.querySelectorAll('.decrease-qty').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();

        const itemId = this.getAttribute('data-item-id');
        let qtyElement = document.getElementById('item-qty-' + itemId);
        let currentQty = parseInt(qtyElement.textContent);

        // Only allow the quantity to decrease if it's greater than 1
        if (currentQty > 1) {
            let newQty = currentQty - 1;
            qtyElement.textContent = newQty; // Update front-end immediately

            // Send update to server
            updateQuantity(itemId, newQty);
        }
    });
});

// Remove an item from the cart
document.querySelectorAll('.close').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();

        const itemId = this.getAttribute('data-item-id');

        // Remove the item from front-end
        document.querySelector('[data-item-id="' + itemId + '"]').closest('.main').remove();

        // Send remove request to server
        removeItem(itemId);
    });
});

// Function to update the quantity on the backend
function updateQuantity(itemId, newQty) {
    fetch('update_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ itemId, quantity: newQty })
    }).then(response => response.json())
    .then(data => {
      if (data.success) {
          console.log('Quantity updated successfully');
      } else {
          console.error('Failed to update quantity: ' + data.message);
      }
  });
}

// Function to remove an item from the backend
function removeItem(itemId) {
    fetch('remove_item.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ itemId })
    }).then(response => response.json())
    .then(data => {
      if (data.success) {
          console.log('Item removed successfully');
      } else {
          console.error('Failed to remove item: ' + data.message);
      }
  });

     function validateForm() { 
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
}


</script>


</body>
</html>
