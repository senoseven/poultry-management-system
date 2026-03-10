<?php
session_start();
include 'connection.php';

if(!isset($_SESSION['customer'])){
    header("Location: customer_login.php");
    exit();
}

$email = $_SESSION['customer'];
$customer = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM customers WHERE email='$email'"));

// HANDLE ORDER
if(isset($_POST['order'])){
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    $product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id='$product_id'"));

    if($product && $product['stock'] >= $quantity){
        $total_price = $product['price'] * $quantity;

        mysqli_query($conn,"INSERT INTO orders (customer_email, product_id, quantity, total_price)
        VALUES('$email','$product_id','$quantity','$total_price')");

        mysqli_query($conn,"UPDATE products SET stock = stock - $quantity WHERE id='$product_id'");

        $success = "Order placed successfully!";
    } else {
        $error = "Not enough stock available!";
    }
}

$products = mysqli_query($conn,"SELECT * FROM products");
$orders = mysqli_query($conn,"
    SELECT orders.*, products.name 
    FROM orders 
    JOIN products ON orders.product_id = products.id
    WHERE customer_email='$email'
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

    <h2>Welcome, <?php echo $customer['name']; ?> 👋</h2>

    <?php if(isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
    <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <!-- PROFILE CARD -->
    <div class="card">
        <h3>My Profile</h3>
        <p>Email: <?php echo $customer['email']; ?></p>
        <p>Member Since: <?php echo $customer['created_at']; ?></p>
    </div>

    <!-- PRODUCTS CARD -->
    <div class="card">
        <h3>Available Products</h3>
        <table>
            <tr>
                <th>Product</th>
                <th>Price (Ksh)</th>
                <th>Stock</th>
                <th>Order</th>
            </tr>
            <?php while($row = mysqli_fetch_assoc($products)){ ?>
            <tr>
                <form method="POST">
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['price']; ?></td>
                    <td><?php echo $row['stock']; ?></td>
                    <td>
                        <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                        <input type="number" name="quantity" min="1" required>
                        <button type="submit" name="order">Buy</button>
                    </td>
                </form>
            </tr>
            <?php } ?>
        </table>
    </div>

    <!-- ORDER HISTORY -->
    <div class="card">
        <h3>My Order History</h3>
        <table>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Total (Ksh)</th>
                <th>Date</th>
            </tr>
            <?php while($order = mysqli_fetch_assoc($orders)){ ?>
            <tr>
                <td><?php echo $order['name']; ?></td>
                <td><?php echo $order['quantity']; ?></td>
                <td><?php echo $order['total_price']; ?></td>
                <td><?php echo $order['order_date']; ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>

    <p><a href="customer_logout.php">Logout</a></p>

</div>

</body>
</html>