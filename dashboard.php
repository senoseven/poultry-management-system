<?php
session_start();
include 'connection.php';

// Redirect if not logged in
if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

// =====================
// Handle Form Submissions
// =====================

// Add Birds
if(isset($_POST['add_bird'])){
    $type = $_POST['type'];
    $quantity = $_POST['quantity'];
    $date = date("Y-m-d");

    mysqli_query($conn,"INSERT INTO birds(type,quantity,date_added)
    VALUES('$type','$quantity','$date')");
}

// Add Feed
if(isset($_POST['add_feed'])){
    $feed_type = $_POST['feed_type'];
    $quantity = $_POST['feed_quantity'];
    $cost = $_POST['feed_cost']; // cost per batch
    $date = date("Y-m-d");

    mysqli_query($conn,"INSERT INTO feed(feed_type,quantity,date)
    VALUES('$feed_type','$quantity','$date')");

    // Optional: Store cost if you want advanced profit calculation
}

// Add Sales
if(isset($_POST['add_sale'])){
    $product = $_POST['product'];
    $quantity = $_POST['sale_quantity'];
    $amount = $_POST['amount'];
    $date = date("Y-m-d");

    mysqli_query($conn,"INSERT INTO sales(product,quantity,amount,date)
    VALUES('$product','$quantity','$amount','$date')");
}

// =====================
// Calculate Dashboard Totals
// =====================

// Total birds
$total_birds = mysqli_fetch_assoc(mysqli_query($conn,"SELECT SUM(quantity) as total FROM birds"))['total'] ?? 0;

// Total feed quantity
$total_feed = mysqli_fetch_assoc(mysqli_query($conn,"SELECT SUM(quantity) as total FROM feed"))['total'] ?? 0;

// Total sales amount
$total_sales = mysqli_fetch_assoc(mysqli_query($conn,"SELECT SUM(amount) as total FROM sales"))['total'] ?? 0;

// For simplicity, assume **feed cost per unit = 50** (You can extend this later)
$feed_cost_per_unit = 50;
$total_feed_cost = $total_feed * $feed_cost_per_unit;

// Calculate profit
$profit = $total_sales - $total_feed_cost;

?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>Poultry Dashboard</h1>
    <a href="logout.php" style="color:white; float:right; margin-right:20px;">Logout</a>
</header>

<div class="container">

<h2>Dashboard Summary</h2>
<p><b>Total Birds:</b> <?php echo $total_birds; ?></p>
<p><b>Total Feed Used:</b> <?php echo $total_feed; ?> units</p>
<p><b>Total Sales:</b> Ksh <?php echo number_format($total_sales,2); ?></p>
<p><b>Total Feed Cost:</b> Ksh <?php echo number_format($total_feed_cost,2); ?></p>
<p><b>Profit:</b> Ksh <?php echo number_format($profit,2); ?></p>

<hr>

<h2>Add Birds</h2>
<form method="POST">
    <input type="text" name="type" placeholder="Bird Type" required>
    <input type="number" name="quantity" placeholder="Quantity" required>
    <button type="submit" name="add_bird">Add Bird</button>
</form>

<h2>Add Feed</h2>
<form method="POST">
    <input type="text" name="feed_type" placeholder="Feed Type" required>
    <input type="number" name="feed_quantity" placeholder="Quantity" required>
    <input type="number" name="feed_cost" placeholder="Cost per batch" required>
    <button type="submit" name="add_feed">Add Feed</button>
</form>

<h2>Add Sale</h2>
<form method="POST">
    <input type="text" name="product" placeholder="Product" required>
    <input type="number" name="sale_quantity" placeholder="Quantity" required>
    <input type="number" step="0.01" name="amount" placeholder="Amount (Ksh)" required>
    <button type="submit" name="add_sale">Add Sale</button>
</form>

<hr>

<h2>Bird Records</h2>
<table>
<tr>
<th>ID</th>
<th>Type</th>
<th>Quantity</th>
<th>Date Added</th>
</tr>

<?php
$result = mysqli_query($conn,"SELECT * FROM birds ORDER BY id DESC");
while($row = mysqli_fetch_assoc($result)){
    echo "<tr>
        <td>".$row['id']."</td>
        <td>".$row['type']."</td>
        <td>".$row['quantity']."</td>
        <td>".$row['date_added']."</td>
    </tr>";
}
?>
</table>

</div>
</body>
</html>