<?php
session_start();

// Giriş kontrolü
if (!isset($_SESSION['username']) || $_SESSION['rol'] !== 'user') {
    header("Location: login.php");
    exit();
}

// Çıkış işlemi
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Örnek veri (Gerçek veriler veritabanından çekilecektir)
$grades = [
    ['subject' => 'Matematik', 'grade' => 85, 'status' => 'Geçti'],
    ['subject' => 'Fizik', 'grade' => 70, 'status' => 'Geçti'],
    ['subject' => 'Kimya', 'grade' => 40, 'status' => 'Kaldı'],
];
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
                <!-- Diğer işlemler buraya eklenebilir -->
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
                    <?php foreach ($grades as $grade): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($grade['subject']); ?></td>
                        <td><?php echo htmlspecialchars($grade['grade']); ?></td>
                        <td><?php echo htmlspecialchars($grade['status']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
