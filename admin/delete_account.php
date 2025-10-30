<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['delete'])) {
    $delete = mysqli_query($conn, "DELETE FROM users WHERE id='$user_id'");
    if ($delete) {
        session_destroy();
        echo "<script>alert('Akun berhasil dihapus.'); window.location='../auth/register.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus akun!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Hapus Akun</title>
</head>
<body>
<h2>Konfirmasi Hapus Akun</h2>
<p>Apakah Anda yakin ingin menghapus akun Anda secara permanen?</p>
<form method="POST">
    <button type="submit" name="delete">Ya, Hapus Akun</button>
    <a href="profile.php">Batal</a>
</form>
</body>
</html>
