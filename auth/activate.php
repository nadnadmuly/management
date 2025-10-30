<?php
include('../config/db.php');

if (isset($_GET['code'])) {
    $activation_code = $_GET['code'];

    $query = "SELECT * FROM users WHERE activation_code='$activation_code' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        if ($user['is_active'] == 1) {
            echo "<script>alert('Akun sudah aktif!'); window.location='login.php';</script>";
        } else {
            $update = "UPDATE users SET is_active=1 WHERE id=" . $user['id'];
            if (mysqli_query($conn, $update)) {
                echo "<script>alert('Akun berhasil diaktifkan! Silakan login.'); window.location='login.php';</script>";
            } else {
                echo "<script>alert('Gagal mengaktifkan akun.');</script>";
            }
        }
    } else {
        echo "<script>alert('Kode aktivasi tidak valid!'); window.location='register.php';</script>";
    }
} else {
    echo "<script>alert('Tidak ada kode aktivasi!'); window.location='login.php';</script>";
}
?>
