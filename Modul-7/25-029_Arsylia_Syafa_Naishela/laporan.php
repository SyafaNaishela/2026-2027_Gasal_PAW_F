<?php
include 'koneksi.php';

$tanggal_mulai = $_GET['tanggal_mulai'] ?? date('Y-m-01');
$tanggal_selesai = $_GET['tanggal_selesai'] ?? date('Y-m-d');

$query = mysqli_prepare($koneksi, "
    SELECT transaksi.*, pelanggan.nama
    FROM transaksi
    LEFT JOIN pelanggan
        ON transaksi.pelanggan_id = pelanggan.id
    WHERE transaksi.waktu_transaksi >= ?
      AND transaksi.waktu_transaksi < DATE_ADD(?, INTERVAL 1 DAY)
    ORDER BY transaksi.waktu_transaksi ASC
");

mysqli_stmt_bind_param(
    $query,
    "ss",
    $tanggal_mulai,
    $tanggal_selesai
);

mysqli_stmt_execute($query);
$data_transaksi = mysqli_stmt_get_result($query);

$query_total = mysqli_prepare($koneksi, "
    SELECT
        COUNT(DISTINCT pelanggan_id) AS jumlah_pelanggan,
        COALESCE(SUM(total), 0) AS total_pendapatan
    FROM transaksi
    WHERE waktu_transaksi >= ?
      AND waktu_transaksi < DATE_ADD(?, INTERVAL 1 DAY)
");

mysqli_stmt_bind_param(
    $query_total,
    "ss",
    $tanggal_mulai,
    $tanggal_selesai
);

mysqli_stmt_execute($query_total);

$ringkasan = mysqli_fetch_assoc(
    mysqli_stmt_get_result($query_total)
);


// Query rekapitulasi berdasarkan tanggal
$query_rekap = mysqli_prepare($koneksi, "
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
    $query_rekap,
    "ss",
    $tanggal_mulai,
    $tanggal_selesai
);

mysqli_stmt_execute($query_rekap);

$data_rekap = mysqli_stmt_get_result($query_rekap);

$rekap_tabel = [];
$label_tanggal = [];
$total_grafik = [];

while ($rekap = mysqli_fetch_assoc($data_rekap)) {

    $rekap_tabel[] = $rekap;

    $label_tanggal[] = $rekap['tanggal'];

    $total_grafik[] = (float) $rekap['total_harian'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 20px;
        }

        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
        }

        h2 {
            text-align: center;
        }

        input, button {
            padding: 8px;
            margin: 5px;
        }

        button {
            cursor: pointer;
            border: none;
            border-radius: 4px;
        }

        .tampil {
            background: #2878d0;
            color: white;
        }

        .cetak {
            background: #555;
            color: white;
        }

        .excel {
            display: inline-block;
            background: #16834a;
            color: white;
            padding: 8px 12px;
            margin: 5px;
            text-decoration: none;
            border-radius: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        th {
            background: #2878d0;
            color: white;
        }

        /* Ringkasan */
        .ringkasan {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }

        .card {
            background: #e8f1ff;
            padding: 20px;
            flex: 1;
            border-radius: 8px;
        }

        .grafik {
            height: 350px;
            margin-top: 20px;
            position: relative;
        }

        @media print {

            @page {
                size: A4;
                margin: 15mm;
            }

            body {
                background: white;
                margin: 0;
                padding: 0;
                color: black;
                font-family: Arial, sans-serif;
            }

            .filter,
            .tombol {
                display: none !important;
            }

            .container {
                width: 100%;
                padding: 0;
                margin: 0;
                background: white;
                border-radius: 0;
                box-shadow: none;
            }

            h2, h3 {
                color: black;
                page-break-after: avoid;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                font-size: 10pt;
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
            }

            th, td {
                border: 1px solid black;
                padding: 7px;
                color: black;
            }

            th {
                background: #ddd !important;
                color: black !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .grafik {
                height: 300px;
                width: 100%;
                page-break-inside: avoid;
            }

            canvas {
                max-width: 100%;
            }

            .ringkasan {
                display: flex;
                gap: 15px;
                page-break-inside: avoid;
            }

            .card {
                background: white !important;
                border: 1px solid black;
                color: black;
                padding: 15px;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

        }

    </style>
</head>
<body>
<div class="container">
    <h2>LAPORAN PENJUALAN</h2>
    <div class="filter">
        <form method="GET">
            <label>Tanggal Mulai:</label>
            <input
                type="date"
                name="tanggal_mulai"
                value="<?php echo htmlspecialchars($tanggal_mulai); ?>"
            >

            <label>Tanggal Selesai:</label>
            <input
                type="date"
                name="tanggal_selesai"
                value="<?php echo htmlspecialchars($tanggal_selesai); ?>"
            >

            <button type="submit" class="tampil">
                Tampilkan
            </button>
        </form>
    </div>

    <h3>Data Transaksi</h3>
    <table>
        <tr>
            <th>No</th>
            <th>ID Transaksi</th>
            <th>Tanggal</th>
            <th>Nama Pelanggan</th>
            <th>Keterangan</th>
            <th>Total</th>
        </tr>

        <?php
        $no = 1;

        while ($transaksi = mysqli_fetch_assoc($data_transaksi)) {
        ?>

        <tr>
            <td><?php echo $no++; ?></td>
            <td>
                <?php echo htmlspecialchars($transaksi['id']); ?>
            </td>
            <td>
                <?php echo htmlspecialchars($transaksi['waktu_transaksi']); ?>
            </td>
            <td>
                <?php
                echo htmlspecialchars($transaksi['nama'] ?? '-');
                ?>
            </td>
            <td>
                <?php
                echo htmlspecialchars($transaksi['keterangan'] ?? '-');
                ?>
            </td>
            <td>
                Rp <?php
                echo number_format(
                    $transaksi['total'],
                    0,
                    ',',
                    '.'
                );
                ?>
            </td>
        </tr>

        <?php } ?>

    </table>

    <h3>Grafik Pendapatan Harian</h3>
    <div class="grafik">
        <canvas id="grafikPenjualan"></canvas>
    </div>

    <h3>Rekapitulasi Penjualan Harian</h3>
    <table>
        <tr>
            <th>No</th>
            <th>Tanggal Transaksi</th>
            <th>Total Pendapatan</th>
        </tr>

        <?php
        $no = 1;

        foreach ($rekap_tabel as $rekap) {
        ?>

        <tr>
            <td><?php echo $no++; ?></td>
            <td>
                <?php echo htmlspecialchars($rekap['tanggal']); ?>
            </td>
            <td>
                Rp <?php
                echo number_format(
                    $rekap['total_harian'],
                    0,
                    ',',
                    '.'
                );
                ?>
            </td>
        </tr>

        <?php } ?>

    </table>
    <div class="ringkasan">
        <div class="card">
            <h3>Jumlah Pelanggan Unik</h3>
            <h2>
                <?php
                echo $ringkasan['jumlah_pelanggan'];
                ?>
            </h2>
        </div>
        <div class="card">
            <h3>Total Pendapatan Keseluruhan</h3>
            <h2>
                Rp <?php
                echo number_format(
                    $ringkasan['total_pendapatan'],
                    0,
                    ',',
                    '.'
                );
                ?>
            </h2>
        </div>
    </div>

    <div class="tombol" style="margin-top: 25px;">
        <button
            type="button"
            class="cetak"
            onclick="window.print()"
        >
            Cetak Laporan
        </button>

        <a
            href="excel.php?tanggal_mulai=<?php echo urlencode($tanggal_mulai); ?>&tanggal_selesai=<?php echo urlencode($tanggal_selesai); ?>"
            class="excel"
        >
            Ekspor Excel
        </a>
    </div>
</div>

<script>
    const tanggal = <?php echo json_encode($label_tanggal); ?>;
    const total = <?php echo json_encode($total_grafik); ?>;
    const ctx = document.getElementById('grafikPenjualan');
    const grafik = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: tanggal,
            datasets: [{
                label: 'Total Pendapatan',
                data: total,
                backgroundColor: '#2878d0',
                borderColor: '#15569c',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Total Pendapatan (Rupiah)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Tanggal Transaksi'
                    }
                }
            }
        }
    });
    window.addEventListener('beforeprint', function () {
        grafik.resize();
    });

    window.addEventListener('afterprint', function () {
        grafik.resize();
    });

</script>
</body>
</html>