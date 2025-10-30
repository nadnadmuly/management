<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

if (isset($_POST['change'])) {
    $old = $_POST['old_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];
    $user_id = $_SESSION['user_id'];

    $check = mysqli_query($conn, "SELECT password FROM users WHERE id='$user_id'");
    $data = mysqli_fetch_assoc($check);

    if (password_verify($old, $data['password'])) {
        if ($new === $confirm) {
            $hash = password_hash($new, PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE users SET password='$hash' WHERE id='$user_id'");
            echo "<script>alert('Password berhasil diubah!'); window.location='profile.php';</script>";
        } else {
            echo "<script>alert('Password baru dan konfirmasi tidak sama!');</script>";
        }
    } else {
        echo "<script>alert('Password lama salah!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Ubah Password</title>
</head>
<body>
<h2>Ubah Password</h2>
<form method="POST">
    Password Lama:<br>
    <input type="password" name="old_password" required><br><br>
    Password Baru:<br>
    <input type="password" name="new_password" required><br><br>
    Konfirmasi Password Baru:<br>
    <input type="password" name="confirm_password" required><br><br>
    <button type="submit" name="change">Simpan Perubahan</button>
</form>
<a href="profile.php">Kembali ke Profil</a>
</body>
</html>
