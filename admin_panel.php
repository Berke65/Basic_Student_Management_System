<?php
session_start();

// Giriş kontrolü
if (!isset($_SESSION['username']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Çıkış işlemi
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/admin_panel.css">
</head>
<body>

    <div class="admin-panel">
        <div class="sidebar">
            <div class="header">
                <br><br>
                <h1>Admin Paneli</h1>
                <br>
                <form action="" method="get">
                    <button type="submit" name="logout" class="logout-btn">Çıkış Yap</button>
                </form>
            </div>
            <h3>Yönetim Menüsü</h3>
            <ul>
                <li><a href="#">Ana Sayfa</a></li>
                <li><a href="admin/add_student.php">Öğrenci / Admin Ekle</a></li>
                <li><a href="admin/enter_grades.php">Not Gir</a></li>
                <li><a href="admin/manage_payments.php">Ödeme Bilgileri</a></li>
                <!-- Diğer işlemler buraya eklenebilir -->
            </ul>
        </div>

        <div class="content">
            <h2>Hoş Geldiniz, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
            <p>Öğrenci ve admin işlemleri yapabilirsiniz.</p>
        </div>
    </div>

</body>
</html>
