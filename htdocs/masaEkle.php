<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$user = "root";
$pass = "root";
$dbname = "CafeControl";

$conn = mysqli_connect($host, $user, $pass, $dbname);

$tableAdded = false; 
$tableDeleted = false; 
$tableUpdated = false; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_table'])) {
        $table_name = $_POST['table_name'];
        $sql = "INSERT INTO tables (table_name) VALUES ('$table_name')";
        if (mysqli_query($conn, $sql)) {
            $tableAdded = true; 
            header("Location: masaEkle.php?added=true");
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } elseif (isset($_POST['delete_table'])) {
        $table_id = $_POST['table_id'];
        
        $sql = "DELETE FROM quantities WHERE table_id = $table_id";
        if (mysqli_query($conn, $sql)) {
            
            $sql = "DELETE FROM tables WHERE table_id = $table_id";
            if (mysqli_query($conn, $sql)) {
                $tableDeleted = true; 
                header("Location: masaEkle.php?deleted=true");
                exit();
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } elseif (isset($_POST['update_table'])) {
        $table_id = $_POST['table_id'];
        $new_table_name = $_POST['new_table_name'];

        $sql = "UPDATE tables SET table_name = '$new_table_name' WHERE table_id = $table_id";
        if (mysqli_query($conn, $sql)) {
            $tableUpdated = true; 
            header("Location: masaEkle.php?updated=true");
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}

$tables = mysqli_query($conn, "SELECT * FROM tables");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masa Ekle/Sil ve Güncelle</title>
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
        <h1 class="text-center mb-4">Masa Ekle/Sil ve Güncelle</h1>
        <form method="POST" class="mb-4 card p-4">
            <div class="mb-3">
                <label for="table_name" class="form-label">Masa Adı</label>
                <input type="text" class="form-control" id="table_name" name="table_name" required>
            </div>
            <button type="submit" name="add_table" class="btn btn-custom">Masa Ekle</button>
        </form>

        <h2 class="mb-3">Mevcut Masalar</h2>
        <ul class="list-group mb-4">
            <?php while ($row = mysqli_fetch_assoc($tables)): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?php echo htmlspecialchars($row['table_name']); ?>
                    <div class="btn-group">
                        <form method="POST" class="d-inline me-2">
                            <input type="hidden" name="table_id" value="<?php echo $row['table_id']; ?>">
                            <input type="text" class="form-control d-inline" name="new_table_name" value="<?php echo htmlspecialchars($row['table_name']); ?>" required>
                            <button type="submit" name="update_table" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Güncelle</button>
                        </form>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="table_id" value="<?php echo $row['table_id']; ?>">
                            <button type="submit" name="delete_table" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i> Sil</button>
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
                    Masa başarıyla eklendi.
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
                    Masa başarıyla silindi.
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
                    Masa adı başarıyla güncellendi.
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
