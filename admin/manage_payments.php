<?php
session_start();
require '../connection.php'; // Veritabanı bağlantısı

// Giriş kontrolü
if (!isset($_SESSION['username']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Çıkış işlemi
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Yapım Aşamasında</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style/add_student.css"> <!-- Admin paneli CSS dosyanıza referans -->
</head>
<body>

    <div class="admin-panel">
        <div class="sidebar">
            <div class="header">
                <h1>Admin Paneli</h1>
                <form action="" method="get">
                    <button type="submit" name="logout" class="logout-btn">Çıkış Yap</button>
                </form>
            </div>
            <h3>Yönetim Menüsü</h3>
            <ul>
                <li><a href="../admin_panel.php">Ana Sayfa</a></li>
                <li><a href="admin/add_student.php">Öğrenci Ekle</a></li>
                <li><a href="enter_grades.php">Not Gir</a></li>
                <li><a href="manage_payments.php">Ödeme Bilgileri</a></li>
            </ul>
        </div>

        <div class="content">
            <h2>Site Yapım Aşamasında</h2>
            <p>Bu sayfa henüz tamamlanmamıştır. Lütfen daha sonra tekrar kontrol edin.</p>
        </div>
    </div>

</body>
</html>
