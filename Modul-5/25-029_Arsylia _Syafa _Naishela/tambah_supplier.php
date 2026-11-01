<?php
include 'koneksi.php';

if (isset($_POST['simpan'])) {
    $nama = $_POST['nama'];
    $telp = $_POST['telp'];
    $alamat = $_POST['alamat'];

    $query = mysqli_prepare(
        $koneksi,
        "INSERT INTO supplier (nama, telp, alamat)
         VALUES (?, ?, ?)"
    );

    mysqli_stmt_bind_param($query, "sss", $nama, $telp, $alamat);

    if (mysqli_stmt_execute($query)) {
        header("Location: supplier.php");
        exit;
    } else {
        echo "Gagal menambahkan supplier: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Tambah Supplier</title>
</head>
<body>

<h2>Tambah Data Supplier</h2>

<form method="POST">
    <p>
        Nama Supplier:<br>
        <input type="text" name="nama" required>
    </p>

    <p>
        Telepon:<br>
        <input type="text" name="telp" required>
    </p>

    <p>
        Alamat:<br>
        <textarea name="alamat" required></textarea>
    </p>

    <button type="submit" name="simpan">Simpan</button>
    <a href="supplier.php">Batal</a>
</form>

</body>
</html>