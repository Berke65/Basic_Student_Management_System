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

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Formdan gelen verileri al
    $username = $_POST['username'];
    $role = $_POST['role'];
    $password = $_POST['password'];
    $email = $_POST['email']; // E-posta alanı

    // Veritabanına ekleme sorgusu
    $query = "INSERT INTO users (username, rol, password, email) VALUES (?, ?, ?, ?)";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('ssss', $username, $role, $password, $email); // 'ssss' string türünde 4 parametre
        if ($stmt->execute()) {
            $success = "Öğrenci başarıyla eklendi.";
        } else {
            $error = "Öğrenci eklenirken bir hata oluştu: " . $conn->error;
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
    <title>Öğrenci Ekle</title>
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
                <li><a href="../admin_panel.php">AnaSayfa</a></li>
                <li><a href="#">Öğrenci / Admin Ekle</a></li>
                <li><a href="enter_grades.php">Not Gir</a></li>
                <li><a href="manage_payments.php">Ödeme Bilgileri</a></li>
            </ul>
        </div>

        <div class="content">
            <h2>Öğrenci Ekle</h2>
            
            <?php if ($error): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <input type="text" name="username" placeholder="Kullanıcı Adı" required>
                <input type="email" name="email" placeholder="E-posta" required> <!-- E-posta alanı -->
                <select name="role" required>
                    <option value="" disabled selected>Rol Seçiniz</option>
                    <option value="user">Kullanıcı</option>
                    <option value="admin">Admin</option>
                </select>
                <input type="password" name="password" placeholder="Şifre" required>
                <button type="submit">Öğrenci Ekle</button>
            </form>
        </div>
    </div>

</body>
</html>
