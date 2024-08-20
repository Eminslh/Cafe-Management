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
    $data = json_decode(file_get_contents('php://input'), true);
    $updates = $data['updates'];
    $table_id = $data['table_id'];

    $success = true;
    foreach ($updates as $update) {
        $prod_id = $update['prodId'];
        $quantity = $update['quantity'];

        $sql = "INSERT INTO quantities (table_id, prod_id, quantity) VALUES ($table_id, $prod_id, $quantity)
                ON DUPLICATE KEY UPDATE quantity = $quantity";

        if (!mysqli_query($conn, $sql)) {
            $success = false;
            echo "Error: " . mysqli_error($conn);
            break;
        }
    }

    if ($success) {
        echo "Success";
    }
}
?>
