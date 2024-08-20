<!DOCTYPE html>
<html lang="en">
<head>
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

    $sql = "SELECT * FROM tables";
    $tables = mysqli_query($conn, $sql);

    if (!$tables) {
        die("Query failed: " . mysqli_error($conn));
    }

    $num_rows = mysqli_num_rows($tables);

    function getTotalPrice($table_id, $conn) {
        $total_query = "
            SELECT SUM(p.price * q.quantity) as total_price
            FROM quantities q
            JOIN products p ON q.prod_id = p.prod_id
            WHERE q.table_id = $table_id
        ";
        $total_result = mysqli_query($conn, $total_query);
        $total = mysqli_fetch_assoc($total_result)['total_price'];
        return $total ? $total : 0;
    }
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KAFE YÖNETİM</title>
    
    <style>
        body {
            background-image: url('https://axwwgrkdco.cloudimg.io/v7/__gmpics__/66bf6299627949efb3c3b7a571d1a00f');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: #fff;
        }

        h1 {
            text-align: center;
            font-size: 72px;
            font-family: 'Fantasy',;
            font-weight: bolder;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            margin-top: 40px;
        }

        .custom-button {
            padding: 10px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: #ffc107;
            color: #000;
            margin: 5px;
            text-decoration: none;
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .custom-button:hover {
            transform: translateY(-2px);
            background-color: #e0a800;
        }

        .adisyon-button, .receipt-button, .order-button {
            background-color: #007bff;
            color: #fff;
        }

        .adisyon-button:hover, .receipt-button:hover, .order-button:hover {
            background-color: #0056b3;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
            position: relative;
            width: 100%;
            padding-top: 60%;
            background-color: rgba(248, 249, 250, 0.8);
            margin: auto;
        }

        .card-body {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 10px;
        }

        .card-title {
            font-size: 24px;
            font-family: 'Arial', sans-serif;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
            word-wrap: break-word;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.5);
        }

        .success-table .card-body {
            background-color: rgba(40, 167, 69, 0.8) !important;
        }

        .danger-table .card-body {
            background-color: rgba(220, 53, 69, 0.8) !important;
        }

        .total-price-box {
            font-size: 20px;
            font-weight: bold;
            margin-top: 10px;
            color: #333;
            word-wrap: break-word;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            width: 100%;
            text-align: center;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <h1>SİPARİŞ EKRANI</h1>
    <div class="text-center">
        <button class="btn btn-secondary" onclick="window.location.href='loginJS.php'">Geri</button>
    </div>
    <div class="container mt-4">
        <div class="row row-cols-1 row-cols-md-3">
            <?php
            if ($num_rows > 0) {
                while ($row = mysqli_fetch_assoc($tables)) {
                    $status_class = $row['status'] == 'dolu' ? 'danger-table' : 'success-table';
                    $status_text = $row['status'] == 'dolu' ? 'Dolu' : 'Müsait';
                    $total_price = getTotalPrice($row['table_id'], $conn);
                    echo '<div class="col mb-4">';
                    echo '<div class="card ' . $status_class . '">';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title">' . htmlspecialchars($row['table_name']) . '</h5>';
                    echo '<div class="d-flex flex-wrap justify-content-center">';
                    echo '<button class="custom-button mb-2" onclick="changeStatus(' . $row['table_id'] . ', this.parentElement.parentElement.parentElement)">' . $status_text . '</button>';
                    echo '<a href="managementJS.php?table_id=' . htmlspecialchars($row['table_id']) . '" class="custom-button adisyon-button mb-2">' . ($row['status'] == 'dolu' ? 'Siparişi Güncelle' : 'Yeni Sipariş') . '</a>';
                    if ($row['status'] == 'dolu') {
                        echo '<button class="custom-button receipt-button mb-2" onclick="customerLeft(' . $row['table_id'] . ', this.parentElement.parentElement.parentElement)">Müşteri Kalktı</button>';
                        echo '<button class="custom-button order-button mb-2" onclick="showOrderModal(' . $row['table_id'] . ')">Ödeme</button>';
                    }
                    echo '</div>';
                    if ($row['status'] == 'dolu') {
                        echo '<div class="total-price-box">Toplam Tutar: ₺' . number_format($total_price, 2) . '</div>';
                    }
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>Hiç masa bulunamadı.</p>';
            }
            ?>
        </div>
    </div>

    
    <div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderModalLabel">Sipariş Onayı</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                </div>
                <div class="modal-body">
                    <h5 id="modal-table-name" data-table-id=""></h5>
                    <h5 id="modal-total-price"></h5>
                    <div class="d-flex justify-content-between mt-3">
                        <button class="btn btn-primary" onclick="submitOrder('Nakit')">Nakit</button>
                        <button class="btn btn-primary" onclick="submitOrder('Kredi Kartı')">Kredi Kartı</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
    <script>
        function changeStatus(tableId, card) {
            var currentStatus = card.classList.contains('danger-table') ? 'dolu' : 'müsait';
            var newStatus = currentStatus === 'dolu' ? 'müsait' : 'dolu';

            $.ajax({
                url: 'updateTableStatus.php',
                method: 'POST',
                data: { table_id: tableId, status: newStatus },
                success: function(response) {
                    if (response.trim() === 'Success') {
                        card.classList.toggle('danger-table');
                        card.classList.toggle('success-table');
                        card.querySelector('.custom-button').innerText = newStatus === 'dolu' ? 'Dolu' : 'Müsait';
                        location.reload();
                    } else {
                        alert('Hata: ' + response);
                    }
                }
            });
        }

        function customerLeft(tableId, card) {
            $.ajax({
                url: 'processPayment.php',
                method: 'POST',
                data: { table_id: tableId },
                success: function(response) {
                    if (response.trim() === "Success") {
                        alert('Masa başarıyla temizlendi ve müşteri kalktı olarak işaretlendi.');
                        changeStatus(tableId, card);
                    } else {
                        alert('Hata: ' + response);
                    }
                }
            });
        }

        function showOrderModal(tableId) {
            $.ajax({
                url: 'getReceiptDetails.php',
                method: 'POST',
                data: { table_id: tableId },
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.error) {
                        alert(data.error);
                    } else {
                        var orderModal = new bootstrap.Modal(document.getElementById('orderModal'), { backdrop: 'static' });
                        document.getElementById('modal-table-name').textContent = data.table_name + ' Adisyonu';
                        document.getElementById('modal-table-name').setAttribute('data-table-id', tableId);
                        document.getElementById('modal-total-price').textContent = 'Toplam Tutar: ₺' + data.total_price.toFixed(2);
                        orderModal.show();
                    }
                },
                error: function(xhr, status, error) {
                    alert('Sipariş detayı alınırken bir hata oluştu: ' + error);
                }
            });
        }

        function submitOrder(method) {
            var tableId = document.getElementById('modal-table-name').getAttribute('data-table-id');
            $.ajax({
                url: 'zRaporuHandler.php',
                method: 'POST',
                data: { table_id: tableId },
                success: function(response) {
                    if (response.trim() === "Success") {
                        alert(method + ' ile sipariş başarıyla alındı.');
                        location.reload();
                    } else {
                        alert('Hata: ' + response);
                    }
                }
            });
        }

        function processPayment(tableId) {
            $.ajax({
                url: 'processPayment.php',
                method: 'POST',
                data: { table_id: tableId },
                success: function(response) {
                    if (response.trim() === "Success") {
                        alert('Adisyon işlemi başarılı toplam tutar masa içerisinde bildirilmiştir.');
                        location.reload();
                    } else {
                        alert('Hata: ' + response);
                    }
                }
            });
        }

        function zRaporu(tableId) {
            $.ajax({
                url: 'zRaporuHandler.php',
                method: 'POST',
                data: { table_id: tableId },
                success: function(response) {
                    if (response.trim() === "Success") {
                        alert('Z Raporu başarıyla oluşturuldu.');
                    } else {
                        alert('Hata: ' + response);
                    }
                }
            });
        }
    </script>
</body>
</html>
