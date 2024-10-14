<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['rol'] !== 'user') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ../login.php");
    exit();
}

include('../connection.php');

$username = $_SESSION['username'];

$sql = "SELECT id, username, password, email FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    $user_id = $user['id'];
    $username = $user['username'];
    $email = $user['email'];
    $password = $user['password']; 
} else {
    header("Location: logout.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $new_username = $_POST['username'];
    $new_email = $_POST['email'];
    $new_password = $_POST['password'];

    if (!empty($new_password)) {
        $updated_password = $new_password; 
    } else {
        $updated_password = $password;
    }

    $update_sql = "UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssi", $new_username, $new_email, $updated_password, $user_id);
    $update_stmt->execute();

    $_SESSION['username'] = $new_username;
    $_SESSION['email'] = $new_email;

    $success_message = "Bilgileriniz başarıyla güncellenmiştir.";

    $username = $new_username;
    $email = $new_email;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Bilgilerini Güncelle</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../style/update_profile.css">
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
            <li><a href="../user_panel.php">Notlarımı Gör</a></li>
            <li><a href="update_profile.php">Profil Bilgilerimi Güncelle</a></li>
        </ul>
    </div>

    <div class="content">
        <h2>Profil Bilgilerinizi Güncelleyin</h2>

        <?php if (isset($success_message)): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label for="username">Kullanıcı Adı:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">E-posta:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Şifre:</label>
                <input type="password" id="password" name="password">
            </div>
            <button type="submit" class="update-btn">Güncelle</button>
        </form>
    </div>
</div>

</body>
</html>
