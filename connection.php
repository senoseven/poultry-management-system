<?php
$conn = mysqli_connect("localhost", "root", "", "poultry_db");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>