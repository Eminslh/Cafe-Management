<?php
// Hata raporlamasını etkinleştirin
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

// Ürünler
$products = [
    ["Hamburger", 12.00],
    ["Su", 1.00],
    ["Pasta", 5.00],
    ["Makarna", 4.00],
    ["Cola", 4.00],
    ["Pizza", 15.00],
    ["Çay", 2.00],
    ["Kahve", 3.00]
];

// Masaları al
$sql = "SELECT * FROM tables";
$tables = mysqli_query($conn, $sql);

if ($tables) {
    while ($table = mysqli_fetch_assoc($tables)) {
        $table_id = $table['table_id'];
        foreach ($products as $product) {
            $prod_name = $product[0];
            $price = $product[1];
            
            // Ürünün zaten var olup olmadığını kontrol et
            $check_sql = "SELECT * FROM orders WHERE table_id = $table_id AND prod_name = '$prod_name'";
            $check_result = mysqli_query($conn, $check_sql);
            
            if (mysqli_num_rows($check_result) == 0) {
                // Ürünü ekle
                $insert_sql = "INSERT INTO orders (table_id, prod_name, price, quantity) VALUES ($table_id, '$prod_name', $price, 0)";
                if (!mysqli_query($conn, $insert_sql)) {
                    echo "Error: " . mysqli_error($conn);
                }
            }
        }
    }
    echo "Initialization complete.";
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
