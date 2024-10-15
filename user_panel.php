<?php
session_start();
require 'connection.php'; 

// Kullanıcı oturum kontrolü
if (!isset($_SESSION['username']) || $_SESSION['rol'] !== 'user') {
    header("Location: login.php");
    exit();
}

// Kullanıcı çıkış işlemi
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$ogr_ad = $_SESSION['username'];
$query = "SELECT dersler.ders_ad, notes.ara1, notes.ara2, notes.ara3, notes.vize, notes.final, notes.genel_ortalama, notes.lesson_status 
          FROM notes 
          JOIN dersler ON notes.ders_id = dersler.ders_id 
          WHERE notes.ogr_ad = ?";
$grades = [];

if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param('s', $ogr_ad); 
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $grades[] = $row; 
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Paneli</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/user_panel.css">
</head>
<body>

    <div class="admin-panel">
        <div class="sidebar">
            <div class="header">
                <br><br>
                <h1>Kullanıcı Paneli</h1>
                <br>
                <form action="" method="get">
                    <button type="submit" name="logout" class="logout-btn">Çıkış Yap</button>
                </form>
            </div>
            <h3>Hesap Menüsü</h3>
            <ul>
                <li><a href="user_panel.php">Notlarımı Gör</a></li>
                <li><a href="user/update_profile.php">Profil Bilgilerimi Güncelle</a></li>
            </ul>
        </div>

        <div class="content">
            <h2>Hoş Geldiniz, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
            <p>Notlarınızı ve durumunuzu aşağıda görebilirsiniz.</p>

            <h3>Notlarınız</h3>
            <table>
                <thead>
                    <tr>
                        <th>Ders</th>
                        <th>Ara 1</th>
                        <th>Ara 2</th>
                        <th>Ara 3</th>
                        <th>Vize</th>
                        <th>Final</th>
                        <th>Genel Ortalama</th>
                        <th>Durum</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($grades)): ?>
                        <tr>
                            <td colspan="8">Henüz not girilmedi.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($grades as $grade): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($grade['ders_ad']); ?></td>
                            <td><?php echo ($grade['ara1'] == 0) ? "Sınav Yapılmadı" : htmlspecialchars($grade['ara1']); ?></td>
                            <td><?php echo ($grade['ara2'] == 0) ? "Sınav Yapılmadı" : htmlspecialchars($grade['ara2']); ?></td>
                            <td><?php echo ($grade['ara3'] == 0) ? "Sınav Yapılmadı" : htmlspecialchars($grade['ara3']); ?></td>
                            <td><?php echo htmlspecialchars($grade['vize']); ?></td>
                            <td><?php echo htmlspecialchars($grade['final']); ?></td>
                            <td><?php echo htmlspecialchars($grade['genel_ortalama']); ?></td>
                            <td><?php echo htmlspecialchars($grade['lesson_status']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
