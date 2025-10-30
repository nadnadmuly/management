<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include __DIR__ . '/../config/db.php';
include __DIR__ . '/../includes/functions.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = sanitize($_POST['fullname']);
    $email = sanitize($_POST['email']);
    $password = sanitize($_POST['password']);

    if (!empty($fullname) && !empty($email) && !empty($password)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Cek email
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "Email sudah terdaftar!";
        } else {
            $stmt->close();
            $activation_code = bin2hex(random_bytes(16));
            $is_active = 0;

            $stmt = $conn->prepare(
                "INSERT INTO users (fullname, email, password, activation_code, is_active) VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("ssssi", $fullname, $email, $password_hash, $activation_code, $is_active);

            if ($stmt->execute()) {
                $message = "Registrasi berhasil! Silakan cek email untuk aktivasi akun.";
            } else {
                $message = "Error: " . $stmt->error;
            }
        }

        $stmt->close();
        $conn->close();
    } else {
        $message = "Semua field wajib diisi!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi Pengguna</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f5f5f5; margin: 0; padding: 40px; }
        .container { max-width: 400px; background: #fff; padding: 30px; margin: auto; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input { width: 100%; padding: 10px; margin-top: 5px; border-radius: 4px; border: 1px solid #ccc; }
        button { width: 100%; padding: 12px; margin-top: 20px; border: none; border-radius: 4px; background-color: #007BFF; color: white; font-size: 16px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        .message { margin-top: 15px; text-align: center; font-weight: bold; color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Form Registrasi Admin Gudang</h2>
        <?php if($message != ""): ?>
            <div class="message <?php echo strpos($message, 'Error') !== false || strpos($message, 'wajib') !== false ? 'error' : ''; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="">
            <label>Nama Lengkap</label>
            <input type="text" name="fullname" placeholder="Masukkan nama lengkap" required>

            <label>Email</label>
            <input type="email" name="email" placeholder="Masukkan email" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="Masukkan password" required>

            <button type="submit">Daftar</button>
        </form>
    </div>
</body>
</html>
