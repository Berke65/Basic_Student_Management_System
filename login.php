<?php
session_start();
require 'connection.php'; 

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Rolü ve kullanıcı bilgilerini al
    $role = $_POST['role'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Kullanıcının seçtiği role göre sorgu oluştur
    $query = "SELECT * FROM users WHERE username = ? AND rol = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('ss', $username, $role);  
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            
            // Şifre kontrolü
            if ($password === $row['password']) {
                // Kullanıcı bilgilerini oturuma kaydet
                $_SESSION['username'] = $row['username'];
                $_SESSION['rol'] = $row['rol']; // 'role' yerine 'rol' kullanmalıyız
                
                // Rol kontrolü ve yönlendirme
                if ($row['rol'] === 'admin') {
                    header(header: "Location: admin_panel.php"); // Admin paneline yönlendir
                } else {
                    header(header: "Location: user_panel.php"); // Kullanıcı paneline yönlendir
                }
                exit();
            } else {
                $error = "Hatalı kullanıcı adı veya şifre.";
            }
        } else {
            $error = "Kullanıcı bulunamadı veya rol uyumsuz.";
        }

        $stmt->close();
    } else {
        $error = "Veritabanı hatası.";
    }
}

// Eğer kullanıcı giriş yapmışsa, paneline yönlendir
if (isset($_SESSION['username'])) {
    if ($_SESSION['rol'] === 'admin') {
        header("Location: admin_panel.php");
    } else {
        header("Location: user_panel.php");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/login.css"> <!-- CSS dosyanız -->
</head>
<body>

    <div class="admin-panel">
        <div class="login-container">
            <h1>Giriş Yap</h1>
            
            <?php if ($error): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <select name="role" required>
                    <option value="" disabled selected>Rol Seçiniz</option>
                    <option value="admin">Admin</option>
                    <option value="user">Kullanıcı</option>
                </select>
                <input type="text" name="username" placeholder="Kullanıcı Adı" required>
                <input type="password" name="password" placeholder="Şifre" required>
                <button type="submit">Giriş Yap</button>
            </form>
        </div>
    </div>

</body>
</html>
