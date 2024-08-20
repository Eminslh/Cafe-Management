<?php
session_start();
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $sql = "SELECT * FROM users WHERE username = ? AND password = ? AND role = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $password, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;
        if ($role == 'garson') {
            header("Location: garsonPage.php");
        } elseif ($role == 'mudur') {
            header("Location: mudurPage.php");
        }
        exit();
    } else {
        $error = "Yanlış kullanıcı adı veya şifre!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
        }
        .container {
            background-color: rgba(0, 0, 0, 0.7);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            max-width: 450px;
            width: 100%;
        }
        h2 {
            margin-bottom: 30px;
            font-weight: bold;
            text-align: center;
        }
        .btn-custom {
            font-size: 1.2rem;
            padding: 12px;
            margin-bottom: 15px;
            width: 100%;
            border-radius: 25px;
            transition: background-color 0.3s, transform 0.3s;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }
        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
        }
        .form-group label {
            font-weight: bold;
            margin-top: 10px;
        }
        .form-control {
            background-color: #fff;
            border: none;
            padding: 10px;
            font-size: 1rem;
            border-radius: 25px;
        }
        .alert {
            margin-top: 10px;
            border-radius: 25px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>HİSAR CAFE GİRİŞ</h2>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <button type="button" class="btn btn-custom btn-primary mb-3" id="garsonButton" onclick="selectRole('garson')">Garson Girişi</button>
    <button type="button" class="btn btn-custom btn-secondary" id="mudurButton" onclick="selectRole('mudur')">Müdür Girişi</button>
    <form id="loginForm" method="post" style="display: none;">
        <div class="form-group">
            <label for="username">Kullanıcı Adı:</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Şifre:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <input type="hidden" id="role" name="role">
        <button type="submit" class="btn btn-custom btn-primary">GİRİŞ YAP</button>
        <button type="button" class="btn btn-custom btn-secondary mt-3" onclick="goBack()">GERİ</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    function selectRole(role) {
        document.getElementById('garsonButton').style.display = 'none';
        document.getElementById('mudurButton').style.display = 'none';
        document.getElementById('loginForm').style.display = 'block';
        document.getElementById('role').value = role;
    }

    function goBack() {
        document.getElementById('loginForm').style.display = 'none';
        document.getElementById('garsonButton').style.display = 'block';
        document.getElementById('mudurButton').style.display = 'block';
    }
</script>

</body>
</html>
