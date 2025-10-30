<?php
session_start();
include('../config/db.php');
include('../includes/functions.php');

// Cek apakah user sudah login
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

// ===== CREATE (Tambah Produk) =====
if (isset($_POST['add'])) {
    $name = sanitize($_POST['name']);
    $stock = (int) $_POST['stock'];
    $price = (float) $_POST['price'];
    $description = sanitize($_POST['description']);

    $query = "INSERT INTO products (name, stock, price, description) 
              VALUES ('$name', '$stock', '$price', '$description')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Produk berhasil ditambahkan!');</script>";
    } else {
        echo "<script>alert('Gagal menambahkan produk: " . mysqli_error($conn) . "');</script>";
    }
}

// ===== DELETE (Hapus Produk) =====
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $query = "DELETE FROM products WHERE id = $id";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Produk dihapus!');</script>";
    }
}

// ===== UPDATE (Edit Produk) =====
if (isset($_POST['update'])) {
    $id = (int) $_POST['id'];
    $name = sanitize($_POST['name']);
    $stock = (int) $_POST['stock'];
    $price = (float) $_POST['price'];
    $description = sanitize($_POST['description']);

    $query = "UPDATE products SET 
                name='$name', 
                stock='$stock', 
                price='$price', 
                description='$description' 
              WHERE id=$id";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Produk berhasil diperbarui!');</script>";
    }
}

// ===== READ (Tampilkan Produk) =====
$result = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Produk</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 10px; }
        table, th, td { border: 1px solid #999; padding: 8px; text-align: left; }
        input, textarea { width: 100%; padding: 5px; }
        button { padding: 6px 12px; cursor: pointer; }
        h2 { color: #0066cc; }
    </style>
</head>
<body>
    <h2>Manajemen Produk</h2>
    <a href="dashboard.php">⬅ Kembali ke Dashboard</a>
    <br><br>

    <!-- Form Tambah Produk -->
    <form method="POST">
        <h3>Tambah Produk Baru</h3>
        <input type="text" name="name" placeholder="Nama Produk" required><br><br>
        <input type="number" name="stock" placeholder="Stok" required><br><br>
        <input type="number" step="0.01" name="price" placeholder="Harga" required><br><br>
        <textarea name="description" placeholder="Deskripsi Produk"></textarea><br><br>
        <button type="submit" name="add">Tambah</button>
    </form>

    <hr>

    <!-- Tabel Produk -->
    <h3>Daftar Produk</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Stok</th>
            <th>Harga</th>
            <th>Deskripsi</th>
            <th>Aksi</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['name'] ?></td>
            <td><?= $row['stock'] ?></td>
            <td><?= $row['price'] ?></td>
            <td><?= $row['description'] ?></td>
            <td>
                <a href="?edit=<?= $row['id'] ?>">Edit</a> | 
                <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Hapus produk ini?')">Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <!-- Form Edit Produk -->
    <?php if (isset($_GET['edit'])): 
        $id = (int) $_GET['edit'];
        $data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id = $id"));
    ?>
    <hr>
    <form method="POST">
        <h3>Edit Produk</h3>
        <input type="hidden" name="id" value="<?= $data['id'] ?>">
        <input type="text" name="name" value="<?= $data['name'] ?>" required><br><br>
        <input type="number" name="stock" value="<?= $data['stock'] ?>" required><br><br>
        <input type="number" step="0.01" name="price" value="<?= $data['price'] ?>" required><br><br>
        <textarea name="description"><?= $data['description'] ?></textarea><br><br>
        <button type="submit" name="update">Simpan Perubahan</button>
    </form>
    <?php endif; ?>

</body>
</html>
