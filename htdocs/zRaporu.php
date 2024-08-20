<?php
// Hata raporlamasını etkinleştirin
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$user = "root";
$pass = "root"; // Şifrenizi buraya ekleyin
$dbname = "CafeControl";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Z raporu için ürün satış verilerini çekelim
$report_query = "
    SELECT 
        t.table_name,
        r.prod_name, 
        r.price,
        SUM(r.quantity) as total_quantity, 
        SUM(r.price * r.quantity) as total_earnings 
    FROM z_reports r
    JOIN tables t ON r.table_id = t.table_id
    GROUP BY r.table_id, r.prod_name, r.price
    ORDER BY r.table_id, r.prod_name
";
$report_result = mysqli_query($conn, $report_query);

if (!$report_result) {
    die("Query failed: " . mysqli_error($conn));
}

// Toplam ürün ve toplam kazancı hesaplamak için ek sorgu
$total_query = "
    SELECT 
        SUM(r.quantity) as grand_total_quantity, 
        SUM(r.price * r.quantity) as grand_total_earnings 
    FROM z_reports r
";
$total_result = mysqli_fetch_assoc(mysqli_query($conn, $total_query));

// Null değerleri varsayılan değerlere atama
$grand_total_quantity = $total_result['grand_total_quantity'] ?? 0;
$grand_total_earnings = $total_result['grand_total_earnings'] ?? 0.0;

// Verileri masaya göre gruplandır
$report_data = [];
$table_totals = [];
while ($row = mysqli_fetch_assoc($report_result)) {
    $report_data[$row['table_name']][] = $row;
    if (!isset($table_totals[$row['table_name']])) {
        $table_totals[$row['table_name']] = ['total_quantity' => 0, 'total_earnings' => 0.0];
    }
    $table_totals[$row['table_name']]['total_quantity'] += $row['total_quantity'];
    $table_totals[$row['table_name']]['total_earnings'] += $row['total_earnings'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Z Raporu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            padding: 20px;
            max-width: 900px;
            width: 100%;
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .btn-custom {
            background-color: #007bff;
            border: none;
            border-radius: 50px;
            padding: 10px 20px;
            color: white;
        }
        .btn-custom:hover {
            background-color: #0056b3;
        }
        .btn-info {
            background-color: #17a2b8;
            border: none;
            border-radius: 50px;
            padding: 10px 20px;
            color: white;
        }
        .btn-info:hover {
            background-color: #117a8b;
        }
        .btn-danger, .btn-secondary, .btn-primary {
            border-radius: 50px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Z Raporu</h1>
        <?php foreach ($report_data as $table_name => $table_reports): ?>
            <h2 class="mb-3"><?php echo htmlspecialchars($table_name); ?> Masası</h2>
            <table class="table table-striped mb-4">
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
                            <td><?php echo htmlspecialchars(number_format($row['price'], 2)); ?> $</td>
                            <td><?php echo htmlspecialchars($row['total_quantity']); ?></td>
                            <td><?php echo htmlspecialchars(number_format($row['total_earnings'], 2)); ?> $</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2">Masa Toplamı</th>
                        <th><?php echo htmlspecialchars($table_totals[$table_name]['total_quantity']); ?> Adet</th>
                        <th><?php echo htmlspecialchars(number_format($table_totals[$table_name]['total_earnings'], 2)); ?> $</th>
                    </tr>
                </tfoot>
            </table>
        <?php endforeach; ?>
        <table class="table table-striped">
            <tfoot>
                <tr>
                    <th colspan="2">Genel Toplam</th>
                    <th><?php echo htmlspecialchars($grand_total_quantity); ?> Adet</th>
                    <th><?php echo htmlspecialchars(number_format($grand_total_earnings, 2)); ?> $</th>
                </tr>
            </tfoot>
        </table>
        <div class="d-flex justify-content-between mt-4">
            <button class="btn btn-secondary" onclick="window.location.href='mudurPage.php'">Geri</button>
            <button class="btn btn-primary" onclick="gunSonu()">Gün Sonu</button>
            <button class="btn btn-info" onclick="window.location.href='zRaporuGecmisi.php'">Z Raporu Geçmişi</button>
        </div>
    </div>
    
    <script>
        function gunSonu() {
            if (confirm("Gün sonu raporu almak ve tüm Z raporlarını sıfırlamak istediğinizden emin misiniz?")) {
                fetch('gunSonu.php', {
                    method: 'POST'
                })
                .then(response => response.text())
                .then(data => {
                    if (data.trim() === "Success") {
                        alert('Gün sonu raporu başarıyla alındı ve tüm Z raporları sıfırlandı.');
                        location.reload();
                    } else {
                        alert('Hata: ' + data);
                    }
                })
                .catch(error => console.error('Hata:', error));
            }
        }
    </script>
</body>
</html>
