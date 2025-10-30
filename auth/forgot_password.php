<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include __DIR__ . '/../config/db.php';
include __DIR__ . '/../includes/functions.php';
include __DIR__ . '/../includes/mail.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);

    // cek email terdaftar
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $token = bin2hex(random_bytes(16));
        $stmt->close();

        // simpan token di password_resets
        $stmt = $conn->prepare("INSERT INTO password_resets (email, reset_code) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $token);
        $stmt->execute();

        // kirim email
        $link = "http://localhost/management/auth/reset_password.php?token=$token";
        $subject = "Reset Password Admin Gudang";
        $body = "Klik link berikut untuk mereset password Anda: <a href='$link'>$link</a>";
        sendEmail($email, $subject, $body);

        $message = "Tautan reset password telah dikirim ke email Anda!";
    } else {
        $message = "Email tidak terdaftar!";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lupa Password</title>
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
        <h2>Lupa Password</h2>
        <?php if($message != ""): ?>
            <div class="message <?php echo strpos($message, 'tidak')!==false ? 'error':''; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <label>Email</label>
            <input type="email" name="email" placeholder="Masukkan email" required>
            <button type="submit">Kirim Tautan Reset</button>
        </form>
    </div>
</body>
</html>
