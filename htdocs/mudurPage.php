<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Müdür Sayfası</title>
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
        .btn-custom {
            font-size: 1.2rem;
            padding: 15px 30px;
            border: none;
            margin: 10px;
        }
        .btn-primary {
            background: #ff7f50;
        }
        .btn-primary:hover {
            background: #ff6347;
        }
        .btn-secondary, .btn-info, .btn-success {
            border: none;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        .btn-info:hover {
            background: #138496;
        }
        .btn-success:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <div class="container mt-5 text-center">
        <h1 class="mb-4">Müdür Sayfası</h1>
        <div class="d-flex justify-content-center mb-4 flex-wrap">
            <button class="btn btn-primary btn-custom" onclick="window.location.href='masaEkle.php'">Masa Ekle/Sil</button>
            <button class="btn btn-secondary btn-custom" onclick="window.location.href='urunEkle.php'">Ürün Ekle/Sil ve Fiyat Güncelle</button>
            <button class="btn btn-info btn-custom" onclick="window.location.href='zRaporu.php'">Z Raporu</button>
            <button class="btn btn-success btn-custom" onclick="window.location.href='kullaniciDegistir.php'">Kullanıcı Adı/Şifre Değiştir</button>
        </div>
        <button class="btn btn-secondary btn-custom" onclick="window.location.href='loginJS.php'">Geri</button>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
