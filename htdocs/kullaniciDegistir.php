<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$user = "root";
$pass = "root";
$dbname = "CafeControl";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role = $_POST['role'];
    $new_username = $_POST['new_username'];
    $new_password = $_POST['new_password'];

    if ($role == 'garson') {
        $sql = "UPDATE users SET username='$new_username', password='$new_password' WHERE role='garson'";
    } elseif ($role == 'mudur') {
        $sql = "UPDATE users SET username='$new_username', password='$new_password' WHERE role='mudur'";
    }

    if (mysqli_query($conn, $sql)) {
        $message = "Kullanıcı adı ve şifre başarıyla güncellendi.";
    } else {
        $message = "Hata: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Adı/Şifre Değiştir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: #fff;
            font-family: 'Arial', sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            margin-bottom: 20px;
            font-size: 2.5rem;
        }
        .form-label {
            font-weight: bold;
        }
        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: #fff;
            box-shadow: none;
        }
        .form-control:focus {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
        }
        .btn-primary {
            background: #ff7f50;
            border: none;
        }
        .btn-primary:hover {
            background: #ff6347;
        }
        .btn-secondary {
            background: #6c757d;
            border: none;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Kullanıcı Adı/Şifre Değiştir</h1>
        <?php if (isset($message)): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="role" class="form-label">Rol Seçin</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="garson">Garson</option>
                    <option value="mudur">Müdür</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="new_username" class="form-label">Yeni Kullanıcı Adı</label>
                <input type="text" class="form-control" id="new_username" name="new_username" required>
            </div>
            <div class="mb-3">
                <label for="new_password" class="form-label">Yeni Şifre</label>
                <input type="password" class="form-control" id="new_password" name="new_password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Güncelle</button>
        </form>
        <button class="btn btn-secondary mt-3 w-100" onclick="window.location.href='mudurPage.php'">Geri</button>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
