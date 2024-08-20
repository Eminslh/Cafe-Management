<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$user = "root";
$pass = "root";
$dbname = "CafeControl";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$prod_id = $_POST['prod_id'];
$quantity = $_POST['quantity'];
$table_id = $_POST['table_id'];

// Insert or update the quantity
$sql = "INSERT INTO quantities (table_id, prod_id, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $table_id, $prod_id, $quantity, $quantity);

if ($stmt->execute()) {
    echo "Success";
} else {
    echo "Error: " . $conn->error;
}


?>
