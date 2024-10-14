<?php
require 'connection.php'; 
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    $query = "SELECT * FROM users WHERE email = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $token = bin2hex(random_bytes(50)); 
            
            $query = "INSERT INTO password_resets (email, token) VALUES (?, ?) ON DUPLICATE KEY UPDATE token=?";
            if ($stmt = $conn->prepare($query)) {
                $stmt->bind_param('sss', $email, $token, $token);
                $stmt->execute();

                $resetLink = "https://yourwebsite.com/resetPassword.php?token=$token"; // site acılınca düzenlenecek

                $subject = "Şifre Sıfırlama Talebi";
                $message = "Şifrenizi sıfırlamak için aşağıdaki bağlantıya tıklayın:\n\n$resetLink";
                $headers = "From: no-reply@yourwebsite.com";

                if (mail($email, $subject, $message, $headers)) {
                    $success = "Şifre sıfırlama talimatları e-posta adresinize gönderildi.";
                } else {
                    $error = "Mail gönderilirken bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
                }
            }
        } else {
            $error = "Bu e-posta adresine sahip bir kullanıcı bulunamadı.";
        }

        $stmt->close();
    } else {
        $error = "Veritabanı hatası.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Şifremi Unuttum</title>
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
    <div class="forgot-password-container">
        <h1>Şifremi Unuttum</h1>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php elseif ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="email" name="email" placeholder="E-posta Adresiniz" required>
            <button type="submit">Gönder</button>
        </form>
    </div>
</body>
</html>
