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

if (isset($_GET['table_id'])) {
    $table_id = $_GET['table_id'];

    $sql = "SELECT p.prod_id, p.prod_name, p.price, p.image_url, COALESCE(q.quantity, 0) as quantity
            FROM products p
            LEFT JOIN quantities q ON p.prod_id = q.prod_id AND q.table_id = $table_id";
    $products = mysqli_query($conn, $sql);

    if (!$products) {
        die("Query failed: " . mysqli_error($conn));
    }

    $table_sql = "SELECT * FROM tables WHERE table_id = $table_id";
    $table = mysqli_fetch_assoc(mysqli_query($conn, $table_sql));
} else {
    die("table_id parametresi eksik.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo strtoupper(htmlspecialchars($table['table_name'])); ?> SİPARİŞLERİ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #ffe6f0; 
        }
        .quantity-input {
            display: flex;
            align-items: center;
        }
        .quantity-input input {
            width: 60px;
            text-align: center;
            margin: 0 10px;
        }
        .quantity-input button {
            border: none;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: white;
            border-radius: 50%;
        }
        .quantity-input button.decrement {
            background-color: #dc3545;
        }
        .quantity-input button.increment {
            background-color: #28a745;
        }
        .quantity-input button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
        .card {
            transition: transform 0.2s ease-in-out;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .modal-header {
            background-color: #f8f9fa;
        }
        .modal-body {
            background-color: #ffffff;
        }
        .modal-footer {
            background-color: #f8f9fa;
        }
        .page-title {
            text-transform: uppercase;
            text-align: center;
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card img {
            max-width: 100%;
            height: 200px; 
            object-fit: cover; 
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1 class="page-title"><?php echo strtoupper(htmlspecialchars($table['table_name'])); ?> SİPARİŞLERİ</h1>
        <a href="garsonPage.php" class="btn btn-secondary mb-3">Geri</a>
        <button class="btn btn-warning mb-3" onclick="confirmResetQuantities()">Ürün Adetlerini Sıfırla</button>
        
        <div class="row">
            <?php while ($row = mysqli_fetch_assoc($products)): ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <?php if ($row['image_url']): ?>
                                <img src="<?php echo htmlspecialchars($row['image_url']); ?>" class="img-fluid mb-3" alt="<?php echo htmlspecialchars($row['prod_name']); ?>">
                            <?php endif; ?>
                            <h5 class="card-title">Ürün: <?php echo htmlspecialchars($row['prod_name']); ?></h5>
                            <p class="card-text">Fiyat: ₺<?php echo htmlspecialchars($row['price']); ?></p>
                            <div class="form-group">
                                <label for="quantity-<?php echo $row['prod_id']; ?>">Adet:</label>
                                <div class="quantity-input">
                                    <button class="decrement" onclick="changeQuantity(<?php echo $row['prod_id']; ?>, -1)">-</button>
                                    <input type="number" class="form-control" id="quantity-<?php echo $row['prod_id']; ?>" name="quantity-<?php echo $row['prod_id']; ?>" value="<?php echo htmlspecialchars($row['quantity']); ?>" onchange="calculateTotal()">
                                    <button class="increment" onclick="changeQuantity(<?php echo $row['prod_id']; ?>, 1)">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <div class="row">
            <div class="col-md-6">
                <button class="btn btn-success" onclick="placeOrder()">Sipariş Al</button>
            </div>
            <div class="col-md-6 text-end">
                <h2 id="total-price">Toplam: ₺0</h2>
            </div>
        </div>
    </div>

    <script>
        function changeQuantity(prodId, change) {
            var quantityInput = document.getElementById('quantity-' + prodId);
            var currentQuantity = parseInt(quantityInput.value);
            var newQuantity = currentQuantity + change;
            if (newQuantity >= 0) {
                quantityInput.value = newQuantity;
                calculateTotal();
            }
        }

        function updateProduct(prodId) {
            var quantity = document.getElementById('quantity-' + prodId).value;
            fetch('updateJS.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `prod_id=${prodId}&quantity=${quantity}&table_id=<?php echo $table_id; ?>`
            })
            .then(response => response.text())
            .then(data => {
                if (data.trim() === "Success") {
                    alert('Ürün başarıyla güncellendi.');
                    calculateTotal(); 
                } else {
                    alert('Hata: ' + data);
                }
            })
            .catch(error => console.error('Hata:', error));
        }

        function calculateTotal() {
            var total = 0;
            document.querySelectorAll('.card').forEach(function(card) {
                var price = parseFloat(card.querySelector('.card-text').textContent.replace('Fiyat: ₺', ''));
                var quantity = parseInt(card.querySelector('input').value);
                total += price * quantity;
            });
            document.getElementById('total-price').textContent = 'Toplam: ₺' + total.toFixed(2);
            return total;
        }

        function placeOrder() {
            var updates = [];
            document.querySelectorAll('.card').forEach(function(card) {
                var prodId = card.querySelector('input').id.split('-')[1];
                var quantity = card.querySelector('input').value;
                updates.push({prodId: prodId, quantity: quantity});
            });

            fetch('updateAllJS.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({updates: updates, table_id: <?php echo $table_id; ?>})
            })
            .then(response => response.text())
            .then(data => {
                if (data.trim() === "Success") {
                    alert('Sipariş başarıyla alındı.');
                    changeTableStatus(<?php echo $table_id; ?>, 'dolu');
                } else {
                    alert('Hata: ' + data);
                }
            })
            .catch(error => console.error('Hata:', error));
        }

        function changeTableStatus(tableId, status) {
            fetch('updateTableStatus.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `table_id=${tableId}&status=${status}`
            })
            .then(response => response.text())
            .then(data => {
                if (data.trim() === "Success") {
                    window.location.href = 'garsonPage.php';
                } else {
                    alert('Hata: ' + data);
                }
            })
            .catch(error => console.error('Hata:', error));
        }

        function confirmResetQuantities() {
            if (confirm('Ürün adetlerini sıfırlamak istediğinizden emin misiniz?')) {
                resetQuantities();
            }
        }

        function resetQuantities() {
            document.querySelectorAll('.card input[type="number"]').forEach(function(input) {
                input.value = 0;
            });
            calculateTotal();
        }

        function updateAllProducts() {
            var updates = [];
            document.querySelectorAll('.card').forEach(function(card) {
                var prodId = card.querySelector('input').id.split('-')[1];
                var quantity = card.querySelector('input').value;
                updates.push({prodId: prodId, quantity: quantity});
            });

            fetch('updateAllJS.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({updates: updates, table_id: <?php echo $table_id; ?>})
            })
            .then(response => response.text())
            .then(data => {
                if (data.trim() === "Success") {
                    alert('Tüm ürünler başarıyla güncellendi.');
                    calculateTotal(); 
                } else {
                    alert('Hata: ' + data);
                }
            })
            .catch(error => console.error('Hata:', error));
        }

        document.addEventListener('DOMContentLoaded', function() {
            calculateTotal(); // Sayfa yüklendiğinde toplamı hesapla
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
