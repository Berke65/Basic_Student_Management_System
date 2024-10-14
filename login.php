<?php
session_start();
require 'connection.php'; 

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $role = $_POST['role']; 
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = ? AND rol = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('ss', $username, $role);  
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            
            if ($password === $row['password']) {

                $_SESSION['username'] = $row['username'];
                $_SESSION['rol'] = $row['rol']; 
                
                if ($row['rol'] === 'admin') {
                    header(header: "Location: admin_panel.php");
                } else {
                    header(header: "Location: user_panel.php"); 
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
    <link rel="stylesheet" href="style/login.css"> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
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

            <a href="forgotPassword.php"><button type="button" class="btn btn-outline-primary">Şifremi unuttum</button>
            </a>

        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
</body>
</html>
