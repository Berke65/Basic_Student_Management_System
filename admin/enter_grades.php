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

$users = [];
$query = "SELECT username FROM users WHERE rol = 'user'";
$result = $conn->query($query);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row['username'];
    }
}

$lessons = [];
$query = "SELECT ders_id, ders_ad, ders_gecme_notu FROM dersler";
$result = $conn->query($query);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $lessons[] = $row;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ogr_ad = $_POST['ogr_ad'];
    $ders_id = $_POST['ders_id'];
    $vize = $_POST['vize'];
    $final = $_POST['final'];
    $ara1 = isset($_POST['ara1']) ? $_POST['ara1'] : null;
    $ara2 = isset($_POST['ara2']) ? $_POST['ara2'] : null;
    $ara3 = isset($_POST['ara3']) ? $_POST['ara3'] : null;

    $query = "SELECT ders_gecme_notu FROM dersler WHERE ders_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $ders_id);
    $stmt->execute();
    $stmt->bind_result($ders_gecme_notu);
    $stmt->fetch();
    $stmt->close();

    $ara_toplam = 0;
    $ara_sayisi = 0;

    if (!empty($ara1)) {
        $ara_toplam += $ara1 * 0.0333;
        $ara_sayisi++;
    }
    if (!empty($ara2)) {
        $ara_toplam += $ara2 * 0.0333;
        $ara_sayisi++;
    }
    if (!empty($ara3)) {
        $ara_toplam += $ara3 * 0.0333;
        $ara_sayisi++;
    }

    $ara_ortalama = $ara_sayisi > 0 ? $ara_toplam : 0;

    $vize_ortalama = $vize * 0.4;
    $final_ortalama = $final * 0.5;

    $genel_ortalama = $ara_ortalama + $vize_ortalama + $final_ortalama;
    $lesson_status = $genel_ortalama >= $ders_gecme_notu ? 'geçti' : 'kaldı';

    $query = "INSERT INTO notes (ders_id, ogr_ad, vize, final, ara1, ara2, ara3, genel_ortalama, lesson_status, gecme_notu) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('ssssssssss', $ders_id, $ogr_ad, $vize, $final, $ara1, $ara2, $ara3, $genel_ortalama, $lesson_status, $ders_gecme_notu);
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
                
                <select name="ders_id" required>
                    <option value="" disabled selected>Ders Seçiniz</option>
                    <?php foreach ($lessons as $lesson): ?>
                        <option value="<?php echo htmlspecialchars($lesson['ders_id']); ?>">
                            <?php echo htmlspecialchars($lesson['ders_ad']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <input type="number" name="ara1" placeholder="1. Ara Sınav Notu (Opsiyonel)">
                <input type="number" name="ara2" placeholder="2. Ara Sınav Notu (Opsiyonel)">
                <input type="number" name="ara3" placeholder="3. Ara Sınav Notu (Opsiyonel)">
                <input type="number" name="vize" placeholder="Vize Notu" required>
                <input type="number" name="final" placeholder="Final Notu" required>
                
                <button type="submit">Notu Ekle</button>
            </form>
        </div>
    </div>
</body>
</html>
