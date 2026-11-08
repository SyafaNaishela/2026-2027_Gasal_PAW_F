<?php
include 'koneksi.php';

$pesan = "";

if (isset($_POST['tambah'])) {

    $kode = trim($_POST['kode_barang']);
    $nama = trim($_POST['nama_barang']);
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    if ($kode == "" || $nama == "" || $harga == "" || $stok == "") {

        $pesan = "Semua data harus diisi!";

    } elseif ($harga < 0 || $stok < 0) {

        $pesan = "Harga dan stok tidak boleh kurang dari 0!";

    } else {

        $cek = mysqli_prepare($koneksi, "
            SELECT id FROM barang
            WHERE kode_barang = ?
        ");

        mysqli_stmt_bind_param($cek, "s", $kode);
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);

        if (mysqli_stmt_num_rows($cek) > 0) {

            $pesan = "Kode barang sudah digunakan!";

        } else {

            $supplier_id = 1;

            $query = mysqli_prepare($koneksi, "
                INSERT INTO barang
                (kode_barang, nama_barang, harga, stok, supplier_id)
                VALUES ('$kode', '$nama', '$harga', '$stok', '$supplier_id')
            ");

            mysqli_stmt_bind_param(
                $query,
                "ssiii",
                $kode,
                $nama,
                $harga,
                $stok,
                $supplier_id
            );

            if (mysqli_stmt_execute($query)) {

                header("Location: barang.php?status=berhasil");
                exit;

            } else {

                $pesan = "Data barang gagal ditambahkan!";
            }
        }
    }
}

$data_barang = mysqli_query($koneksi, "
    SELECT * FROM barang ORDER BY id ASC
");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Barang</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <h2>Data Barang</h2>
    <?php if ($pesan != "") { ?>
        <p class="pesan-error">
            <?= htmlspecialchars($pesan); ?>
        </p>
    <?php } ?>
    <?php if (isset($_GET['status']) && $_GET['status'] == 'berhasil') { ?>
        <p class="pesan-sukses">
            Data barang berhasil ditambahkan!
        </p>
    <?php } ?>
    <div class="form-barang">
        <h3>Tambah Data Barang</h3>
        <form method="POST" action="">
            <label>Kode Barang</label>
            <input type="text"
                   name="kode_barang"
                   maxlength="10"
                   placeholder="Contoh: BR006"
                   required>

            <label>Nama Barang</label>
            <input type="text"
                   name="nama_barang"
                   maxlength="100"
                   placeholder="Masukkan nama barang"
                   required>

            <label>Harga</label>
            <input type="number"
                   name="harga"
                   min="0"
                   placeholder="Masukkan harga barang"
                   required>

            <label>Stok</label>
            <input type="number"
                   name="stok"
                   min="0"
                   placeholder="Masukkan stok barang"
                   required>

            <button type="submit" name="tambah">
                Tambah Barang
            </button>
        </form>
    </div>
    <h3>Daftar Data Barang</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Kode Barang</th>
            <th>Nama Barang</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Aksi</th>
        </tr>

        <?php while ($data = mysqli_fetch_assoc($data_barang)) { ?>
        <tr>
            <td><?= $data['id']; ?></td>
            <td>
                <?= htmlspecialchars($data['kode_barang']); ?>
            </td>
            <td>
                <?= htmlspecialchars($data['nama_barang']); ?>
            </td>
            <td>
                <?= number_format($data['harga'], 0, ',', '.'); ?>
            </td>
            <td>
                <?= $data['stok']; ?>
            </td>
            <td>
                <a href="hapus_barang.php?id=<?= $data['id']; ?>"
                   onclick="return confirm('Yakin ingin menghapus barang ini?')">
                    Hapus
                </a>
            </td>

        </tr>
        <?php } ?>
    </table>
    <br>
    <a href="index.php">Kembali</a>
</body>
</html>