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

// Kullanıcı listesini çekme
$users = [];
$query = "SELECT username FROM users WHERE rol = 'user'";
$result = $conn->query($query);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row['username'];
    }
}

// Form gönderildiğinde not ekleme işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Formdan gelen verileri al
    $ogr_ad = $_POST['ogr_ad']; // Seçilen öğrenci adı
    $lesson_name = $_POST['lesson_name'];
    $lesson_note = $_POST['lesson_note'];
    $lesson_status = $_POST['lesson_status'];

    // Veritabanına ekleme sorgusu
    $query = "INSERT INTO notes (ogr_ad, lesson_name, lesson_note, lesson_status) VALUES (?, ?, ?, ?)";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('ssss', $ogr_ad, $lesson_name, $lesson_note, $lesson_status); // 'ssss' string türünde 4 parametre
        if ($stmt->execute()) {
            $success = "Not başarıyla eklendi.";
        } else {
            $error = "Not eklenirken bir hata oluştu: " . $conn->error;
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
    <title>Not Ekle</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style/enter_grades.css"> <!-- Admin paneli CSS dosyanıza referans -->
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
                <li><a href="add_student.php">Öğrenci Ekle</a></li>
                <li><a href="enter_grades.php">Not Gir</a></li>
                <li><a href="manage_payments.php">Ödeme Bilgileri</a></li>
            </ul>
        </div>

        <div class="content">
            <h2>Not Ekle</h2>
            
            <?php if ($error): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <select name="ogr_ad" required>
                    <option value="" disabled selected>Öğrenci Seçiniz</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?php echo htmlspecialchars($user); ?>"><?php echo htmlspecialchars($user); ?></option>
                    <?php endforeach; ?>
                </select>
                
                <input type="text" name="lesson_name" placeholder="Ders Adı" required>
                <input type="text" name="lesson_note" placeholder="Not" required>
                
                <select name="lesson_status" required>
                    <option value="" disabled selected>Durum Seçiniz</option>
                    <option value="geçti">Geçti</option>
                    <option value="kaldı">Kaldı</option>
                </select>
                
                <button type="submit">Notu Ekle</button>
            </form>
        </div>
    </div>

</body>
</html>
