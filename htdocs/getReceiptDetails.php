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

    $table_sql = "SELECT * FROM tables WHERE table_id = $table_id";
    $table_result = mysqli_query($conn, $table_sql);

    if (!$table_result) {
        die("Table query failed: " . mysqli_error($conn));
    }

    $table = mysqli_fetch_assoc($table_result);

    $sql = "SELECT p.prod_name, q.quantity, p.price
            FROM quantities q
            JOIN products p ON q.prod_id = p.prod_id
            WHERE q.table_id = $table_id";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die("Product query failed: " . mysqli_error($conn));
    }

    $products = [];
    $total_price = 0;

    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
        $total_price += $row['quantity'] * $row['price'];
    }

    if (empty($products)) {
        echo json_encode([
            'error' => 'No products found for this table.'
        ]);
    } else {
        // Daha detaylı bir şekilde ürünlerin listelenmesi
        $product_details = [];
        foreach ($products as $product) {
            $product_details[] = [
                'product_name' => $product['prod_name'],
                'quantity' => $product['quantity'],
                'price' => $product['price'],
                'total_price' => $product['quantity'] * $product['price']
            ];
        }

        echo json_encode([
            'table_name' => $table['table_name'],
            'products' => $product_details, // Ürünlerin detayları
            'total_price' => $total_price
        ]);
    }
}
?>
