<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$user = "root";
$pass = "root";
$dbname = "CafeControl";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $table_id = $_POST['table_id'];

    $sql = "SELECT p.prod_name, q.quantity, p.price
            FROM quantities q
            JOIN products p ON q.prod_id = p.prod_id
            WHERE q.table_id = $table_id";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }

    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }

    if (!empty($products)) {
        foreach ($products as $product) {
            $insert_sql = "INSERT INTO z_reports (table_id, prod_name, quantity, price, created_at)
                           VALUES ($table_id, '{$product['prod_name']}', {$product['quantity']}, {$product['price']}, NOW())";
            if (!mysqli_query($conn, $insert_sql)) {
                die("Insert failed: " . mysqli_error($conn));
            }
        }
        echo "Success";
    } else {
        echo "No products to report.";
    }
}
?>
