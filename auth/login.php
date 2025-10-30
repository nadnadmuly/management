<?php
// Tampilkan semua error untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include __DIR__ . '/../config/db.php';
include __DIR__ . '/../includes/functions.php';

$message = "";

// Proses login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = sanitize($_POST['password']);

    $stmt = $conn->prepare("SELECT id, fullname, password, is_active FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $fullname, $hashed_password, $is_active);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        if ($is_active == 1 && password_verify($password, $hashed_password)) {
            // Login sukses
            $_SESSION['user_id'] = $id;
            $_SESSION['fullname'] = $fullname;
            header("Location: ../dashboard/index.php");
            exit;
        } elseif ($is_active == 0) {
            $message = "Akun belum aktif. Silakan cek email untuk aktivasi.";
        } else {
            $message = "Password salah!";
        }
    } else {
        $message = "Email tidak ditemukan!";
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin Gudang</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 40px; }
        .container { max-width: 400px; background: #fff; padding: 30px; margin: auto; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input { width: 100%; padding: 10px; margin-top: 5px; border-radius: 4px; border: 1px solid #ccc; }
        button { width: 100%; padding: 12px; margin-top: 20px; border: none; border-radius: 4px; background-color: #007BFF; color: white; font-size: 16px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        .message { text-align: center; font-weight: bold; margin-top: 15px; color: red; }
        .forgot { text-align: center; margin-top: 10px; }
        .forgot a { color: #007BFF; text-decoration: none; }
        .forgot a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login Admin Gudang</h2>
        <?php if($message != ""): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label>Email</label>
            <input type="email" name="email" placeholder="Masukkan email" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="Masukkan password" required>

            <button type="submit">Login</button>
        </form>

        <div class="forgot">
            <a href="forgot_password.php">Lupa Password?</a>
        </div>
    </div>
</body>
</html>
