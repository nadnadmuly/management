<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}
$user = $_SESSION['user'];
?>
<h2>Selamat datang, <?php echo $user['fullname']; ?>!</h2>
<a href="profile.php">Profil</a> | 
<a href="products.php">Produk</a> | 
<a href="logout.php">Logout</a>
