<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include __DIR__ . '/../config/db.php';
include __DIR__ . '/../includes/functions.php';

$message = "";
$show_form = false;

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // cek token di database
    $stmt = $conn->prepare("SELECT email FROM password_resets WHERE reset_code = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($email);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        $show_form = true;
    } else {
        $message = "Token tidak valid atau sudah kadaluarsa.";
    }
    $stmt->close();
} else {
    $message = "Token tidak ditemukan.";
}

// proses reset password
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = sanitize($_POST['password']);
    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $password_hash, $email);
    if ($stmt->execute()) {
        // hapus token setelah berhasil
        $stmt->close();
        $stmt = $conn->prepare("DELETE FROM password_resets WHERE reset_code = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();

        $message = "Password berhasil diubah! Silakan login.";
        $show_form = false;
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <style>
        body { font-family: Arial; background:#f5f5f5; padding:40px; }
        .container { max-width: 400px; background:#fff; padding:30px; margin:auto; border-radius:8px; box-shadow:0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align:center; }
        input, button { width:100%; padding:10px; margin-top:10px; border-radius:4px; border:1px solid #ccc; }
        button { background:#007BFF; color:#fff; border:none; cursor:pointer; }
        button:hover { background:#0056b3; }
        .message { text-align:center; font-weight:bold; margin-top:15px; color:green; }
        .error { color:red; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>
        <?php if($message != ""): ?>
            <div class="message <?php echo strpos($message, 'tidak')!==false ? 'error':''; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if($show_form): ?>
        <form method="POST">
            <label>Password Baru</label>
            <input type="password" name="password" placeholder="Masukkan password baru" required>
            <button type="submit">Ubah Password</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
