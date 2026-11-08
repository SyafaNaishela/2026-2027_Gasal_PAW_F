<?php
include 'koneksi.php';

$id = (int) $_GET['id'];

$cek = mysqli_query(
    $koneksi,
    "SELECT * FROM transaksi_detail
     WHERE barang_id = $id"
);

if (mysqli_num_rows($cek) > 0) {

    echo "<script>
        alert('Barang masih digunakan dalam transaksi, tidak bisa dihapus!');
        window.location='barang.php';
    </script>";

} else {

    $hapus = mysqli_query(
        $koneksi,
        "DELETE FROM barang WHERE id = $id"
    );

    if ($hapus) {
        echo "<script>
            alert('Data barang berhasil dihapus!');
            window.location='barang.php';
        </script>";
    } else {
        echo "Data barang gagal dihapus!";
    }

}
?>
