<?php
include 'koneksi.php';

if (isset($_POST['simpan'])) {

    $tanggal = $_POST['tanggal'];
    $keterangan = $_POST['keterangan'];
    $pelanggan = $_POST['pelanggan'];

    $query = mysqli_query($koneksi, "
        INSERT INTO transaksi
        (waktu_transaksi, keterangan, total, pelanggan_id)
        VALUES
        ('$tanggal', '$keterangan', 0, '$pelanggan')
    ");

    if ($query) {
        echo "<script>
            alert('Transaksi berhasil ditambahkan!');
            window.location='transaksi.php';
        </script>";
    }
}

$pelanggan = mysqli_query($koneksi, "SELECT * FROM pelanggan");

$transaksi = mysqli_query($koneksi, "
    SELECT transaksi.*, pelanggan.nama
    FROM transaksi
    JOIN pelanggan ON transaksi.pelanggan_id = pelanggan.id
    ORDER BY transaksi.id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Praktikum Master Detail</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h2>Tambah Transaksi</h2>

<form method="POST">

    <label>Tanggal Transaksi</label><br>
    <input type="date" name="tanggal" required>
    <br><br>

    <label>Keterangan</label><br>
    <textarea name="keterangan"></textarea>
    <br><br>

    <label>Pelanggan</label><br>
    <select name="pelanggan" required>
        <option value="">Pilih Pelanggan</option>

        <?php while ($p = mysqli_fetch_assoc($pelanggan)) { ?>
            <option value="<?= $p['id']; ?>">
                <?= $p['nama']; ?>
            </option>
        <?php } ?>

    </select>

    <br><br>
    <button type="submit" name="simpan">Simpan Transaksi</button>

</form>

<hr>

<h2>Daftar Transaksi</h2>

<table border="1" cellpadding="8" cellspacing="0">

    <tr>
        <th>ID</th>
        <th>Tanggal</th>
        <th>Pelanggan</th>
        <th>Keterangan</th>
        <th>Total</th>
        <th>Aksi</th>
    </tr>

    <?php while ($t = mysqli_fetch_assoc($transaksi)) { ?>
    <tr>
        <td><?= $t['id']; ?></td>
        <td><?= $t['waktu_transaksi']; ?></td>
        <td><?= $t['nama']; ?></td>
        <td><?= $t['keterangan']; ?></td>
        <td>Rp<?= number_format($t['total'], 0, ',', '.'); ?></td>
        <td>
            <a href="detail_transaksi.php?id=<?= $t['id']; ?>">
                Tambah Detail
            </a>
        </td>
    </tr>
    <?php } ?>

</table>

<br>
<a href="index.php">Kembali</a>

</body>
</html>
