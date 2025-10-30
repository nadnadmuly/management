<?php
session_start();
include('../config/db.php');
include('../includes/functions.php');

if (!isLoggedIn()) redirect('../auth/login.php');

$user = $_SESSION['user'];

if (isset($_POST['update'])) {
    $fullname = sanitize($_POST['fullname']);
    $email = sanitize($_POST['email']);

    $query = "UPDATE users SET fullname='$fullname', email='$email' WHERE id=" . $user['id'];
    if (mysqli_query($conn, $query)) {
        $_SESSION['user']['fullname'] = $fullname;
        $_SESSION['user']['email'] = $email;
        echo "<script>alert('Profil berhasil diperbarui!');</script>";
    } else {
        echo "<script>alert('Gagal memperbarui profil!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Profil</title>
</head>
<body>
<h2>Profil Anda</h2>
<form method="POST">
    Nama Lengkap:<br>
    <input type="text" name="fullname" value="<?= $user['fullname'] ?>"><br><br>
    Email:<br>
    <input type="email" name="email" value="<?= $user['email'] ?>"><br><br>
    <button type="submit" name="update">Simpan</button>
</form>
<a href="dashboard.php">Kembali</a>
</body>
</html>
