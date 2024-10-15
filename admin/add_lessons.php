<?php
session_start();
require '../connection.php';

if (!isset($_SESSION['username']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ../login.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $ders_ad = $_POST['ders_ad'];
    $ders_gecme_notu = $_POST['ders_gecme_notu'];

    $query = "INSERT INTO dersler (ders_ad, ders_gecme_notu) VALUES (?, ?)";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('sd', $ders_ad, $ders_gecme_notu); 
        if ($stmt->execute()) {
            $success = "Ders başarıyla eklendi.";
        } else {
            $error = "Ders eklenirken bir hata oluştu: " . $conn->error;
        }
        $stmt->close();
    } else {
        $error = "Veritabanı hatası: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ders Ekle</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style/enter_grades.css">
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
                <li><a href="add_student.php">Öğrenci / Admin Ekle</a></li>
                <li><a href="add_lessons.php">Ders Ekle</a></li>
                <li><a href="enter_grades.php">Not Gir</a></li>
                <li><a href="manage_payments.php">Ödeme Bilgileri</a></li>
            </ul>
        </div>

        <div class="content">
            <h2>Ders Ekle</h2>

            <?php if ($error): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <input type="text" name="ders_ad" placeholder="Ders Adı" required>
                <input type="number" step="0.01" name="ders_gecme_notu" placeholder="Geçme Notu" required>
                <button type="submit">Ders Ekle</button>
            </form>
        </div>
    </div>
</body>
</html>
