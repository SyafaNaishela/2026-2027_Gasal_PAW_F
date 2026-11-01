<?php
include 'koneksi.php';

$id = (int) ($_GET['id'] ?? 0);

$query = mysqli_prepare(
    $koneksi,
    "DELETE FROM supplier WHERE id = ?"
);

mysqli_stmt_bind_param($query, "i", $id);

if (mysqli_stmt_execute($query)) {
    header("Location: supplier.php");
    exit;
} else {
    echo "Gagal menghapus supplier. Supplier mungkin masih digunakan oleh data barang.";
}
?>