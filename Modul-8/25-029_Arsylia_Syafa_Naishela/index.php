<?php
include 'cek_login.php';

$judul = "Aplikasi Penjualan";
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $judul; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <h2><?php echo $judul; ?></h2>

    <p>
        Selamat datang,
        <b><?php echo htmlspecialchars($_SESSION['nama']); ?></b>
    </p>

    <?php if ($_SESSION['level'] == 1) { ?>

        <h3>Menu Owner</h3>

        <a href="index.php">Home</a>
        <br><br>

        <a href="barang.php">Data Barang</a>
        <br><br>

        <a href="supplier.php">Data Supplier</a>
        <br><br>

        <a href="pelanggan.php">Data Pelanggan</a>
        <br><br>

        <a href="user.php">Data User</a>
        <br><br>

        <a href="transaksi.php">Transaksi</a>
        <br><br>

        <a href="laporan.php">Laporan</a>
        <br><br>

    <?php } else { ?>

        <h3>Menu Kasir</h3>

        <a href="index.php">Home</a>
        <br><br>

        <a href="transaksi.php">Transaksi</a>
        <br><br>

        <a href="laporan.php">Laporan</a>
        <br><br>

    <?php } ?>

    <hr>

    <a href="logout.php">Logout</a>

</body>
</html>
