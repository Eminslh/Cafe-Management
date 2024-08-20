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


$history_query = "
    SELECT 
        report_date, 
        table_name, 
        prod_name, 
        price,
        SUM(quantity) as total_quantity, 
        SUM(price * quantity) as total_earnings 
    FROM z_reports_history r
    JOIN tables t ON r.table_id = t.table_id
    GROUP BY report_date, table_name, prod_name, price
    ORDER BY report_date DESC, table_name, prod_name
";
$history_result = mysqli_query($conn, $history_query);

if (!$history_result) {
    die("Query failed: " . mysqli_error($conn));
}


$history_data = [];
while ($row = mysqli_fetch_assoc($history_result)) {
    $history_data[$row['report_date']][$row['table_name']][] = $row;
}


$daily_totals = [];
$grand_totals = [];

foreach ($history_data as $date => $tables) {
    $daily_totals[$date] = ['total_quantity' => 0, 'total_earnings' => 0];

    foreach ($tables as $table => $records) {
        $table_total_quantity = 0;
        $table_total_earnings = 0;

        foreach ($records as $record) {
            $table_total_quantity += $record['total_quantity'];
            $table_total_earnings += $record['total_earnings'];
        }

        $daily_totals[$date]['total_quantity'] += $table_total_quantity;
        $daily_totals[$date]['total_earnings'] += $table_total_earnings;

        $grand_totals[$date][$table] = ['total_quantity' => $table_total_quantity, 'total_earnings' => $table_total_earnings];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Z Raporu Geçmişi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .report-box {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
        }
        .report-box h2, .report-box h3, .report-box h4 {
            margin-top: 0;
        }
        .total-box {
            background-color: #e9ecef;
            padding: 10px;
            border-radius: 8px;
            margin-top: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .total-box h4 {
            margin: 0;
            color: green;
        }
        .total-box p {
            margin: 0;
            color: black;
            font-weight: bold;
        }
        .table-info {
            background-color: #d1ecf1;
            border-color: #bee5eb;
        }
        .general-total-box {
            background-color: #ffdddd;
            padding: 10px;
            border-radius: 8px;
            margin-top: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .general-total-box h4 {
            margin: 0;
            color: red;
        }
        .general-total-box p {
            margin: 0;
            color: black;
            font-weight: bold;
        }
        .report-date {
            color: purple;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Z Raporu Geçmişi</h1>
        <?php foreach ($history_data as $report_date => $tables): ?>
            <div class="report-box">
                <h2 class="mb-3 report-date"><?php echo htmlspecialchars($report_date); ?> Tarihi</h2>
                <?php foreach ($tables as $table_name => $table_reports): ?>
                    <?php if (!empty($table_reports)): ?>
                        <h3 class="mb-3" style="color: green;"><?php echo htmlspecialchars($table_name); ?> </h3>
                        <table class="table table-info mb-4">
                            <thead>
                                <tr>
                                    <th>Ürün Adı</th>
                                    <th>Birim Fiyatı</th>
                                    <th>Satılan Adet</th>
                                    <th>Toplam Kazanç</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($table_reports as $row): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['prod_name']); ?></td>
                                        <td><?php echo htmlspecialchars(number_format($row['price'], 2)); ?> ₺</td>
                                        <td><?php echo htmlspecialchars($row['total_quantity']); ?></td>
                                        <td><?php echo htmlspecialchars(number_format($row['total_earnings'], 2)); ?> ₺</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div class="total-box">
                            <h4>Toplam (<?php echo htmlspecialchars($table_name); ?> ):</h4>
                            <p>Satılan Adet: <?php echo htmlspecialchars($grand_totals[$report_date][$table_name]['total_quantity']); ?></p>
                            <p>Toplam Kazanç: <?php echo htmlspecialchars(number_format($grand_totals[$report_date][$table_name]['total_earnings'], 2)); ?> ₺</p>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
                <div class="general-total-box">
                    <h4>Genel Toplam (<?php echo htmlspecialchars($report_date); ?> Tarihi):</h4>
                    <p>Toplam Satılan Adet: <?php echo htmlspecialchars($daily_totals[$report_date]['total_quantity']); ?></p>
                    <p>Toplam Kazanç: <?php echo htmlspecialchars(number_format($daily_totals[$report_date]['total_earnings'], 2)); ?>₺ </p>
                </div>
            </div>
        <?php endforeach; ?>
        <button class="btn btn-secondary" onclick="window.location.href='zRaporu.php'">Geri</button>
    </div>
</body>
</html>
