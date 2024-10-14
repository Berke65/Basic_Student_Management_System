<?php
session_start();
require 'connection.php'; 

if (!isset($_SESSION['username']) || $_SESSION['rol'] !== 'user') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$ogr_ad = $_SESSION['username'];
$query = "SELECT lesson_name, lesson_note, lesson_status FROM notes WHERE ogr_ad = ?";
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
                        <th>Not</th>
                        <th>Durum</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($grades)): ?>
                        <tr>
                            <td colspan="3">Henüz not girilmedi.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($grades as $grade): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($grade['lesson_name']); ?></td>
                            <td><?php echo htmlspecialchars($grade['lesson_note']); ?></td>
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
