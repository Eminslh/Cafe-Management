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
    // Yöntemi kontrol etmek için gelen POST verilerini alın
    // İsteğe bağlı olarak işlem yapabilirsiniz
    // $method = $_POST['method'];

    $reset_quantity_sql = "UPDATE quantities SET quantity = 0 WHERE table_id = $table_id";
    if (mysqli_query($conn, $reset_quantity_sql)) {
        echo "Success";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
