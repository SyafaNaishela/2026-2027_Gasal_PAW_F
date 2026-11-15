<?php
include 'koneksi.php';

$tanggal_mulai = $_GET['tanggal_mulai'] ?? date('Y-m-01');
$tanggal_selesai = $_GET['tanggal_selesai'] ?? date('Y-m-d');

$query = mysqli_prepare($koneksi, "
    SELECT
        DATE(waktu_transaksi) AS tanggal,
        SUM(total) AS total_harian
    FROM transaksi
    WHERE waktu_transaksi >= ?
      AND waktu_transaksi < DATE_ADD(?, INTERVAL 1 DAY)
    GROUP BY DATE(waktu_transaksi)
    ORDER BY tanggal ASC
");

mysqli_stmt_bind_param(
    $query,
    "ss",
    $tanggal_mulai,
    $tanggal_selesai
);

mysqli_stmt_execute($query);

$data_rekap = mysqli_stmt_get_result($query);

$nama_file = "Rekapitulasi_Penjualan_" .
    $tanggal_mulai . "_sampai_" .
    $tanggal_selesai . ".xls";

header("Content-Type: application/vnd.ms-excel; charset=utf-8");

header(
    "Content-Disposition: attachment; filename=\"$nama_file\""
);

header("Cache-Control: no-cache, must-revalidate");

header("Pragma: no-cache");

echo "\xEF\xBB\xBF";
?>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body>
    <h2>REKAPITULASI PENJUALAN HARIAN</h2>
    <p>
        Periode:
        <?php echo htmlspecialchars($tanggal_mulai); ?>
        sampai
        <?php echo htmlspecialchars($tanggal_selesai); ?>
    </p>
    <table border="1">
        <tr>
            <th>No</th>
            <th>Tanggal Transaksi</th>
            <th>Total Pendapatan</th>
        </tr>

        <?php
        $no = 1;
        $total_keseluruhan = 0;

        while ($rekap = mysqli_fetch_assoc($data_rekap)) {

            $total_keseluruhan += $rekap['total_harian'];
        ?>

        <tr>
            <td>
                <?php echo $no++; ?>
            </td>

            <td>
                <?php echo htmlspecialchars($rekap['tanggal']); ?>
            </td>

            <td>
                <?php echo $rekap['total_harian']; ?>
            </td>

        </tr>

        <?php } ?>

        <tr>

            <th colspan="2">Total Keseluruhan</th>

            <th>
                <?php echo $total_keseluruhan; ?>
            </th>

        </tr>

    </table>
</body>
</html>