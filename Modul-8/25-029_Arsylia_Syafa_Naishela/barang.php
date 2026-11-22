<?php
require_once 'cek_owner.php';
require_once 'koneksi.php';

$pesan = "";

if (isset($_POST['tambah'])) {

    $kode = trim($_POST['kode_barang']);
    $nama = trim($_POST['nama_barang']);
    $harga = (int) $_POST['harga'];
    $stok = (int) $_POST['stok'];

    if ($kode == "" || $nama == "" || $_POST['harga'] == "" || $_POST['stok'] == "") {

        $pesan = "Semua data harus diisi!";

    } elseif ($harga < 0 || $stok < 0) {

        $pesan = "Harga dan stok tidak boleh kurang dari 0!";

    } else {

        $cek = mysqli_prepare($koneksi, "
            SELECT id FROM barang WHERE kode_barang = ?
        ");

        mysqli_stmt_bind_param($cek, "s", $kode);
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);

        if (mysqli_stmt_num_rows($cek) > 0) {

            $pesan = "Kode barang sudah digunakan!";

        } else {

            // Supplier default mengikuti kode sebelumnya
            $supplier_id = 1;

            $query = mysqli_prepare($koneksi, "
                INSERT INTO barang
                (kode_barang, nama_barang, harga, stok, supplier_id)
                VALUES (?, ?, ?, ?, ?)
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

                $pesan = "Barang gagal ditambahkan!";
            }
        }
    }
}

if (isset($_POST['update'])) {

    $id = (int) $_POST['id'];
    $kode = trim($_POST['kode_barang']);
    $nama = trim($_POST['nama_barang']);
    $harga = (int) $_POST['harga'];
    $stok = (int) $_POST['stok'];

    if ($kode == "" || $nama == "" || $_POST['harga'] == "" || $_POST['stok'] == "") {

        $pesan = "Semua data harus diisi!";

    } elseif ($harga < 0 || $stok < 0) {

        $pesan = "Harga dan stok tidak boleh kurang dari 0!";

    } else {

        $cek = mysqli_prepare($koneksi, "
            SELECT id FROM barang
            WHERE kode_barang = ? AND id != ?
        ");

        mysqli_stmt_bind_param($cek, "si", $kode, $id);
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);

        if (mysqli_stmt_num_rows($cek) > 0) {

            $pesan = "Kode barang sudah digunakan!";

        } else {

            $query = mysqli_prepare($koneksi, "
                UPDATE barang
                SET kode_barang = ?, nama_barang = ?,
                    harga = ?, stok = ?
                WHERE id = ?
            ");

            mysqli_stmt_bind_param(
                $query,
                "ssiii",
                $kode,
                $nama,
                $harga,
                $stok,
                $id
            );

            if (mysqli_stmt_execute($query)) {

                header("Location: barang.php?status=edit");
                exit;

            } else {

                $pesan = "Data barang gagal diperbarui!";
            }
        }
    }
}

if (isset($_POST['hapus'])) {

    $id = (int) $_POST['id'];

    $query = mysqli_prepare($koneksi, "
        DELETE FROM barang WHERE id = ?
    ");

    mysqli_stmt_bind_param($query, "i", $id);

    if (mysqli_stmt_execute($query)) {

        header("Location: barang.php?status=hapus");
        exit;

    } else {

        header("Location: barang.php?status=gagal_hapus");
        exit;
    }
}

$data_edit = null;

if (isset($_GET['edit'])) {

    $id_edit = (int) $_GET['edit'];

    $query_edit = mysqli_prepare($koneksi, "
        SELECT * FROM barang WHERE id = ?
    ");

    mysqli_stmt_bind_param($query_edit, "i", $id_edit);
    mysqli_stmt_execute($query_edit);

    $hasil_edit = mysqli_stmt_get_result($query_edit);
    $data_edit = mysqli_fetch_assoc($hasil_edit);

    if (!$data_edit) {
        header("Location: barang.php");
        exit;
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
        <p class="pesan-sukses">Barang berhasil ditambahkan!</p>
    <?php } ?>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'edit') { ?>
        <p class="pesan-sukses">Barang berhasil diperbarui!</p>
    <?php } ?>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'hapus') { ?>
        <p class="pesan-sukses">Barang berhasil dihapus!</p>
    <?php } ?>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'gagal_hapus') { ?>
        <p class="pesan-error">
            Barang gagal dihapus karena mungkin masih digunakan dalam transaksi!
        </p>
    <?php } ?>


    <div class="form-barang">

        <h3>
            <?= $data_edit ? 'Edit Barang' : 'Tambah Data Barang'; ?>
        </h3>

        <form method="POST" action="barang.php<?= $data_edit ? '?edit=' . $data_edit['id'] : ''; ?>">

            <?php if ($data_edit) { ?>
                <input type="hidden"
                       name="id"
                       value="<?= $data_edit['id']; ?>">
            <?php } ?>

            <label>Kode Barang</label>
            <input type="text"
                   name="kode_barang"
                   maxlength="10"
                   value="<?= htmlspecialchars($data_edit['kode_barang'] ?? ''); ?>"
                   placeholder="Contoh: BR006"
                   required>

            <label>Nama Barang</label>
            <input type="text"
                   name="nama_barang"
                   maxlength="100"
                   value="<?= htmlspecialchars($data_edit['nama_barang'] ?? ''); ?>"
                   placeholder="Masukkan nama barang"
                   required>

            <label>Harga</label>
            <input type="number"
                   name="harga"
                   min="0"
                   value="<?= $data_edit['harga'] ?? ''; ?>"
                   placeholder="Masukkan harga"
                   required>

            <label>Stok</label>
            <input type="number"
                   name="stok"
                   min="0"
                   value="<?= $data_edit['stok'] ?? ''; ?>"
                   placeholder="Masukkan stok"
                   required>

            <button type="submit"
                    name="<?= $data_edit ? 'update' : 'tambah'; ?>">

                <?= $data_edit ? 'Simpan Perubahan' : 'Tambah Barang'; ?>

            </button>

            <?php if ($data_edit) { ?>
                <a href="barang.php">Batal Edit</a>
            <?php } ?>

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

            <td><?= htmlspecialchars($data['kode_barang']); ?></td>

            <td><?= htmlspecialchars($data['nama_barang']); ?></td>

            <td><?= number_format($data['harga'], 0, ',', '.'); ?></td>

            <td><?= $data['stok']; ?></td>

            <td>

                <a href="barang.php?edit=<?= $data['id']; ?>">
                    Edit
                </a>

                |

                <form method="POST"
                      action="barang.php"
                      style="display:inline;"
                      onsubmit="return confirm('Yakin ingin menghapus barang ini?')">

                    <input type="hidden"
                           name="id"
                           value="<?= $data['id']; ?>">

                    <button type="submit" name="hapus">
                        Hapus
                    </button>

                </form>

            </td>

        </tr>

        <?php } ?>

    </table>

    <br>

    <a href="index.php">Kembali</a>

</body>
</html>
