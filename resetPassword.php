<?php
require 'connection.php'; 

$error = '';
$success = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];

        if ($newPassword === $confirmPassword) {

            $query = "SELECT email FROM password_resets WHERE token = ?";
            if ($stmt = $conn->prepare($query)) {
                $stmt->bind_param('s', $token);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $email = $row['email'];

                    $query = "UPDATE users SET password = ? WHERE email = ?";
                    if ($stmt = $conn->prepare($query)) {
                        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
                        $stmt->bind_param('ss', $hashedPassword, $email);
                        $stmt->execute();

                        $query = "DELETE FROM password_resets WHERE token = ?";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param('s', $token);
                        $stmt->execute();

                        $success = "Şifreniz başarıyla güncellendi. Giriş yapabilirsiniz.";
                    }
                } else {
                    $error = "Geçersiz veya süresi dolmuş bağlantı.";
                }

                $stmt->close();
            }
        } else {
            $error = "Şifreler eşleşmiyor.";
        }
    }
} else {
    $error = "Geçersiz talep.";
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Şifre Sıfırlama</title>
    <link rel="stylesheet" href="style/login.css">
    <style>
        body {
    font-family: 'Roboto', sans-serif;
    background-color: #f0f4f8; 
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

.container {
    background: #ffffff; 
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    max-width: 400px;
    width: 100%;
    text-align: center;
}

h1 {
    color: #007bff; 
    margin-bottom: 20px;
}

input, select {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border-radius: 5px;
    border: 1px solid #ddd;
    box-sizing: border-box;
}

button {
    background-color: #007bff;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    width: 100%;
}

button:hover {
    background-color: #0056b3;
}

.error {
    color: #ff4d4d; 
    margin-bottom: 15px;
}

.success {
    color: #28a745; 
    margin-bottom: 15px;
}

.forgot-password {
    display: block;
    margin-top: 15px;
    text-decoration: none;
    color: #007bff;
    transition: color 0.3s ease;
}

.forgot-password:hover {
    color: #0056b3;
}

    </style>
</head>
<body>
    <div class="reset-password-container">
        <h1>Yeni Şifre Belirle</h1>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php elseif ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="password" name="new_password" placeholder="Yeni Şifre" required>
            <input type="password" name="confirm_password" placeholder="Yeni Şifre Tekrar" required>
            <button type="submit">Şifreyi Güncelle</button>
        </form>
    </div>
</body>
</html>
