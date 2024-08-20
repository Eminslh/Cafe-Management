<?php
// Hata raporlamasını etkinleştirin
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$user = "root";
$pass = "root";
$dbname = "CafeControl";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$productAdded = false;
$productDeleted = false;
$priceUpdated = false;
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_product'])) {
        $prod_name = $_POST['prod_name'];
        $price = $_POST['price'];
        $image_url = $_POST['image_url']; // Resim URL'sini POST verisinden alıyoruz

        $check_sql = "SELECT * FROM products WHERE prod_name = '$prod_name'";
        $check_result = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($check_result) > 0) {
            $error = "Aynı isimde bir ürün zaten mevcut!";
        } else {
            $sql = "INSERT INTO products (prod_name, price, image_url) VALUES ('$prod_name', $price, '$image_url')";
            if (mysqli_query($conn, $sql)) {
                $productAdded = true;
                header("Location: urunEkle.php?added=true");
                exit();
            } else {
                $error = "Ürün eklenirken bir hata oluştu: " . mysqli_error($conn);
            }
        }
    } elseif (isset($_POST['delete_product'])) {
        $prod_id = $_POST['prod_id'];

        // İlk olarak quantities tablosundaki ilgili kayıtları sil
        $delete_quantities_sql = "DELETE FROM quantities WHERE prod_id = $prod_id";
        mysqli_query($conn, $delete_quantities_sql);

        // Ardından products tablosundaki ürünü sil
        $sql = "DELETE FROM products WHERE prod_id = $prod_id";
        if (mysqli_query($conn, $sql)) {
            $productDeleted = true;
            header("Location: urunEkle.php?deleted=true");
            exit();
        } else {
            $error = "Ürün silinirken bir hata oluştu: " . mysqli_error($conn);
        }
    } elseif (isset($_POST['update_price'])) {
        $prod_id = $_POST['prod_id'];
        $new_price = $_POST['new_price'];

        $sql = "UPDATE products SET price = $new_price WHERE prod_id = $prod_id";
        if (mysqli_query($conn, $sql)) {
            $priceUpdated = true;
            header("Location: urunEkle.php?updated=true");
            exit();
        } else {
            $error = "Fiyat güncellenirken bir hata oluştu: " . mysqli_error($conn);
        }
    }
}

$products = mysqli_query($conn, "SELECT * FROM products");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ürün Ekle/Sil ve Fiyat Güncelle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        body {
            background-color: #e9ecef;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            padding: 20px;
            max-width: 800px;
            width: 100%;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            background-color: #ffffff;
            padding: 20px;
            margin-bottom: 20px;
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
        .list-group-item {
            border: none;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .modal-content {
            border-radius: 10px;
        }
        .form-control {
            border-radius: 50px;
        }
        .btn-close {
            background-color: #f8d7da;
            border-radius: 50%;
        }
        .btn-danger, .btn-warning {
            border-radius: 50px;
        }
        .btn-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
    </style>
    <script>
        window.onload = function() {
            if (<?php echo isset($_GET['added']) && $_GET['added'] === 'true' ? 'true' : 'false'; ?>) {
                var myModal = new bootstrap.Modal(document.getElementById('addedModal'), {});
                myModal.show();
                history.replaceState(null, null, window.location.pathname);
            }
            if (<?php echo isset($_GET['deleted']) && $_GET['deleted'] === 'true' ? 'true' : 'false'; ?>) {
                var myModal = new bootstrap.Modal(document.getElementById('deletedModal'), {});
                myModal.show();
                history.replaceState(null, null, window.location.pathname);
            }
            if (<?php echo isset($_GET['updated']) && $_GET['updated'] === 'true' ? 'true' : 'false'; ?>) {
                var myModal = new bootstrap.Modal(document.getElementById('updatedModal'), {});
                myModal.show();
                history.replaceState(null, null, window.location.pathname);
            }
        }
    </script>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Ürün Ekle/Sil ve Fiyat Güncelle</h1>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" class="mb-4 card p-4">
            <div class="mb-3">
                <label for="prod_name" class="form-label">Ürün Adı</label>
                <input type="text" class="form-control" id="prod_name" name="prod_name" required>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Fiyat</label>
                <input type="number" class="form-control" id="price" name="price" step="0.01" required>
            </div>
            <div class="mb-3">
                <label for="image_url" class="form-label">Resim URL</label>
                <input type="text" class="form-control" id="image_url" name="image_url">
            </div>
            <button type="submit" name="add_product" class="btn btn-custom">Ürün Ekle</button>
        </form>

        <h2 class="mb-3">Mevcut Ürünler</h2>
        <ul class="list-group mb-4">
            <?php while ($row = mysqli_fetch_assoc($products)): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <?php echo htmlspecialchars($row['prod_name']); ?> - ₺<?php echo htmlspecialchars($row['price']); ?>
                        <?php if ($row['image_url']): ?>
                            <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="Ürün Resmi" style="width: 50px; height: auto; margin-left: 10px;">
                        <?php endif; ?>
                    </div>
                    <div class="btn-group">
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="prod_id" value="<?php echo $row['prod_id']; ?>">
                            <button type="submit" name="delete_product" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i> Sil</button>
                        </form>
                        <form method="POST" class="d-inline ms-3">
                            <input type="hidden" name="prod_id" value="<?php echo $row['prod_id']; ?>">
                            <input type="number" class="form-control d-inline" name="new_price" step="0.01" value="<?php echo htmlspecialchars($row['price']); ?>" required style="width: 100px;">
                            <button type="submit" name="update_price" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Güncelle</button>
                        </form>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>

        <button class="btn btn-secondary" onclick="window.location.href='mudurPage.php'">Geri</button>
    </div>

    <div class="modal fade" id="addedModal" tabindex="-1" aria-labelledby="addedModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addedModalLabel">Başarılı</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Ürün başarıyla eklendi.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tamam</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deletedModal" tabindex="-1" aria-labelledby="deletedModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deletedModalLabel">Başarılı</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Ürün başarıyla silindi.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tamam</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="updatedModal" tabindex="-1" aria-labelledby="updatedModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updatedModalLabel">Başarılı</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Ürün fiyatı başarıyla güncellendi.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tamam</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
