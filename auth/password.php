<?php
include('../config/db.php');
include('../includes/functions.php');

if (isset($_POST['reset'])) {
    $email = sanitize($_POST['email']);
    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

    if (mysqli_num_rows($check) > 0) {
        $user = mysqli_fetch_assoc($check);
        $token = md5(rand());
        mysqli_query($conn, "UPDATE users SET activation_code='$token' WHERE email='$email'");

        $reset_link = "http://localhost/MANAGEMENT/auth/reset_password.php?token=$token";
        echo "<script>alert('Tautan reset password: $reset_link');</script>"; 
        // catatan: ini bisa kamu ganti nanti dengan fungsi kirim email sebenarnya
    } else {
        echo "<script>alert('Email tidak ditemukan!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Lupa Password</title>
</head>
<body>
<h2>Lupa Password</h2>
<form method="POST">
    Masukkan Email:<br>
    <input type="email" name="email" required><br><br>
    <button type="submit" name="reset">Kirim Link Reset</button>
</form>
<a href="login.php">Kembali ke Login</a>
</body>
</html>
