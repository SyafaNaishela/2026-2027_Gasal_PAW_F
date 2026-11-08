<?php
include 'koneksi.php';

$id = (int) $_GET['id'];

$transaksi = mysqli_query($koneksi, "
    SELECT * FROM transaksi WHERE id = $id
");

$data_transaksi = mysqli_fetch_assoc($transaksi);

if (!$data_transaksi) {
    die("Transaksi tidak ditemukan!");
}

if (isset($_POST['simpan'])) {

    $barang_id = (int) $_POST['barang_id'];
    $qty = (int) $_POST['qty'];


    $cek = mysqli_query($koneksi, "
        SELECT * FROM transaksi_detail
        WHERE transaksi_id = $id
        AND barang_id = $barang_id
    ");

    if (mysqli_num_rows($cek) > 0) {

        echo "<script>
            alert('Barang sudah dimasukkan!');
            window.location='detail_transaksi.php?id=$id';
        </script>";

    } else {

        $barang = mysqli_query($koneksi, "
            SELECT * FROM barang WHERE id = $barang_id
        ");

        $data_barang = mysqli_fetch_assoc($barang);

        if ($data_barang && $qty > 0) {

            $harga = $data_barang['harga'];

            mysqli_query($koneksi, "
                INSERT INTO transaksi_detail
                (transaksi_id, barang_id, harga, qty)
                VALUES
                ($id, $barang_id, $harga, $qty)
            ");

            mysqli_query($koneksi, "
                UPDATE transaksi
                SET total = (
                    SELECT IFNULL(SUM(harga * qty), 0)
                    FROM transaksi_detail
                    WHERE transaksi_id = $id
                )
                WHERE id = $id
            ");

            echo "<script>
                alert('Detail transaksi berhasil ditambahkan!');
                window.location='detail_transaksi.php?id=$id';
            </script>";

        } else {
            echo "<script>
                alert('Jumlah barang tidak valid!');
                window.location='detail_transaksi.php?id=$id';
            </script>";
        }
    }
}


$barang = mysqli_query($koneksi, "
    SELECT * FROM barang
    WHERE id NOT IN (
        SELECT barang_id
        FROM transaksi_detail
        WHERE transaksi_id = $id
    )
");

$detail = mysqli_query($koneksi, "
    SELECT transaksi_detail.*, barang.nama_barang
    FROM transaksi_detail
    JOIN barang
    ON transaksi_detail.barang_id = barang.id
    WHERE transaksi_detail.transaksi_id = $id
");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Praktikum Master Detail</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h2>Detail Transaksi</h2>
<p>
    ID Transaksi: <?= $data_transaksi['id']; ?>
</p>
<p>
    Tanggal: <?= $data_transaksi['waktu_transaksi']; ?>
</p>
<hr>
<h3>Tambah Barang</h3>
<form method="POST">
    <label>Pilih Barang</label><br>

    <select name="barang_id" required>
        <option value="">Pilih Barang</option>

        <?php while ($b = mysqli_fetch_assoc($barang)) { ?>

            <option value="<?= $b['id']; ?>">
                <?= $b['nama_barang']; ?>
                - Rp<?= number_format($b['harga'], 0, ',', '.'); ?>
            </option>

        <?php } ?>

    </select>
    <br><br>
    <label>Jumlah Barang</label><br>
    <input type="number" name="qty" min="1" required>
    <br><br>
    <button type="submit" name="simpan">
        Tambah Barang
    </button>
</form>
<hr>
<h3>Daftar Detail Barang</h3>
<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>Nama Barang</th>
        <th>Harga</th>
        <th>Qty</th>
        <th>Subtotal</th>
    </tr>

    <?php while ($d = mysqli_fetch_assoc($detail)) { ?>
    <tr>
        <td><?= $d['nama_barang']; ?></td>
        <td>
            Rp<?= number_format($d['harga'], 0, ',', '.'); ?>
        </td>
        <td><?= $d['qty']; ?></td>
        <td>
            Rp<?= number_format(
                $d['harga'] * $d['qty'], 0, ',', '.'
            ); ?>
        </td>
    </tr>

    <?php } ?>

</table>
<br>
<h3>
    Total Transaksi:
    Rp<?= number_format($data_transaksi['total'], 0, ',', '.'); ?>
</h3>
<a href="transaksi.php">Kembali ke Transaksi</a>
</body>
</html>
