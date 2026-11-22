<?php
require_once 'cek_login.php';
require_once 'koneksi.php';

$tanggal_awal = $_GET['tanggal_awal'] ?? '';
$tanggal_akhir = $_GET['tanggal_akhir'] ?? '';

if ($tanggal_awal != '' && $tanggal_akhir != '') {

    $query = mysqli_prepare($koneksi, "
        SELECT transaksi.*, pelanggan.nama
        FROM transaksi
        LEFT JOIN pelanggan ON transaksi.pelanggan_id = pelanggan.id
        WHERE transaksi.waktu_transaksi BETWEEN ? AND ?
        ORDER BY transaksi.id DESC
    ");

    mysqli_stmt_bind_param($query, "ss", $tanggal_awal, $tanggal_akhir);
    mysqli_stmt_execute($query);
    $hasil = mysqli_stmt_get_result($query);

    $hitung = mysqli_prepare($koneksi, "
        SELECT COUNT(*), COALESCE(SUM(total), 0)
        FROM transaksi
        WHERE waktu_transaksi BETWEEN ? AND ?
    ");

    mysqli_stmt_bind_param($hitung, "ss", $tanggal_awal, $tanggal_akhir);
    mysqli_stmt_execute($hitung);
    mysqli_stmt_bind_result($hitung, $jumlah_transaksi, $total_pendapatan);
    mysqli_stmt_fetch($hitung);

} else {

    $hasil = mysqli_query($koneksi, "
        SELECT transaksi.*, pelanggan.nama
        FROM transaksi
        LEFT JOIN pelanggan ON transaksi.pelanggan_id = pelanggan.id
        ORDER BY transaksi.id DESC
    ");

    $ringkasan = mysqli_query($koneksi, "
        SELECT COUNT(*) AS jumlah, COALESCE(SUM(total), 0) AS pendapatan
        FROM transaksi
    ");

    $data_ringkasan = mysqli_fetch_assoc($ringkasan);

    $jumlah_transaksi = $data_ringkasan['jumlah'];
    $total_pendapatan = $data_ringkasan['pendapatan'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h2>Laporan Penjualan</h2>

<form method="GET" action="laporan_penjualan.php">

    <label>Tanggal Awal</label><br>
    <input type="date" name="tanggal_awal"
        value="<?php echo htmlspecialchars($tanggal_awal); ?>">

    <br><br>

    <label>Tanggal Akhir</label><br>
    <input type="date" name="tanggal_akhir"
        value="<?php echo htmlspecialchars($tanggal_akhir); ?>">

    <br><br>

    <button type="submit">Tampilkan Laporan</button>
    <a href="laporan_penjualan.php">Reset</a>

</form>

<br>

<button onclick="window.print()">Cetak Laporan</button>

<hr>

<h3>Ringkasan Penjualan</h3>

<p>Jumlah Transaksi: <?php echo $jumlah_transaksi; ?></p>

<p>Total Pendapatan:
    Rp <?php echo number_format($total_pendapatan); ?>
</p>

<hr>

<h3>Daftar Transaksi</h3>

<table border="1" cellpadding="8" cellspacing="0">

    <tr>
        <th>No</th>
        <th>ID Transaksi</th>
        <th>Tanggal</th>
        <th>Pelanggan</th>
        <th>Barang Dibeli</th>
        <th>Total</th>
        <th>Keterangan</th>
    </tr>

    <?php
    $no = 1;

    while ($t = mysqli_fetch_assoc($hasil)) {
    ?>

    <tr>
        <td><?php echo $no++; ?></td>

        <td><?php echo $t['id']; ?></td>

        <td><?php echo $t['waktu_transaksi']; ?></td>

        <td><?php echo htmlspecialchars($t['nama'] ?? '-'); ?></td>

        <td>
            <?php
            $id_transaksi = (int)$t['id'];

            $detail = mysqli_query($koneksi, "
                SELECT barang.nama_barang, detail_transaksi.harga,
                       detail_transaksi.qty
                FROM detail_transaksi
                JOIN barang ON detail_transaksi.barang_id = barang.id
                WHERE detail_transaksi.transaksi_id = $id_transaksi
            ");

            while ($d = mysqli_fetch_assoc($detail)) {
                echo htmlspecialchars($d['nama_barang']);
                echo " (" . $d['qty'] . " x Rp ";
                echo number_format($d['harga']) . ")<br>";
            }
            ?>
        </td>

        <td>Rp <?php echo number_format($t['total']); ?></td>

        <td><?php echo htmlspecialchars($t['keterangan'] ?? '-'); ?></td>
    </tr>

    <?php } ?>

</table>

<br>

<a href="index.php">Kembali</a>

</body>
</html>
