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
    $current_date = date('Y-m-d');

    
    $history_query = "
        INSERT INTO z_reports_history (report_date, table_id, prod_name, quantity, price)
        SELECT '$current_date', r.table_id, r.prod_name, r.quantity, r.price
        FROM z_reports r
    ";

    if (!mysqli_query($conn, $history_query)) {
        die("History insertion failed: " . mysqli_error($conn));
    }

    
    $clear_query = "TRUNCATE TABLE z_reports";

    if (mysqli_query($conn, $clear_query)) {
        echo "Success";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
