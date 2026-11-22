<?php
require_once 'cek_owner.php';
require_once 'koneksi.php';

$pesan = "";

if (isset($_POST['tambah'])) {

    $nama = trim($_POST['nama']);
    $telp = trim($_POST['telp']);
    $alamat = trim($_POST['alamat']);

    if ($nama == "") {

        $pesan = "Nama supplier harus diisi!";

    } else {

        $query = mysqli_prepare($koneksi, "
            INSERT INTO supplier (nama, telp, alamat)
            VALUES (?, ?, ?)
        ");

        mysqli_stmt_bind_param(
            $query,
            "sss",
            $nama,
            $telp,
            $alamat
        );

        if (mysqli_stmt_execute($query)) {

            header("Location: supplier.php?status=berhasil");
            exit;

        } else {

            $pesan = "Supplier gagal ditambahkan!";
        }
    }
}

if (isset($_POST['update'])) {

    $id = (int) $_POST['id'];
    $nama = trim($_POST['nama']);
    $telp = trim($_POST['telp']);
    $alamat = trim($_POST['alamat']);

    if ($nama == "") {

        $pesan = "Nama supplier harus diisi!";

    } else {

        $query = mysqli_prepare($koneksi, "
            UPDATE supplier
            SET nama = ?, telp = ?, alamat = ?
            WHERE id = ?
        ");

        mysqli_stmt_bind_param(
            $query,
            "sssi",
            $nama,
            $telp,
            $alamat,
            $id
        );

        if (mysqli_stmt_execute($query)) {

            header("Location: supplier.php?status=edit");
            exit;

        } else {

            $pesan = "Supplier gagal diperbarui!";
        }
    }
}

if (isset($_POST['hapus'])) {

    $id = (int) $_POST['id'];

    $query = mysqli_prepare($koneksi, "
        DELETE FROM supplier WHERE id = ?
    ");

    mysqli_stmt_bind_param($query, "i", $id);

    if (mysqli_stmt_execute($query)) {

        header("Location: supplier.php?status=hapus");
        exit;

    } else {

        header("Location: supplier.php?status=gagal_hapus");
        exit;
    }
}

$data_edit = null;

if (isset($_GET['edit'])) {

    $id_edit = (int) $_GET['edit'];

    $query_edit = mysqli_prepare($koneksi, "
        SELECT * FROM supplier WHERE id = ?
    ");

    mysqli_stmt_bind_param($query_edit, "i", $id_edit);
    mysqli_stmt_execute($query_edit);

    $hasil_edit = mysqli_stmt_get_result($query_edit);
    $data_edit = mysqli_fetch_assoc($hasil_edit);

    if (!$data_edit) {
        header("Location: supplier.php");
        exit;
    }
}

$data_supplier = mysqli_query($koneksi, "
    SELECT * FROM supplier ORDER BY id ASC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Supplier</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <h2>Data Supplier</h2>

    <?php if ($pesan != "") { ?>
        <p class="pesan-error">
            <?= htmlspecialchars($pesan); ?>
        </p>
    <?php } ?>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'berhasil') { ?>
        <p class="pesan-sukses">
            Supplier berhasil ditambahkan!
        </p>
    <?php } ?>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'edit') { ?>
        <p class="pesan-sukses">
            Supplier berhasil diperbarui!
        </p>
    <?php } ?>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'hapus') { ?>
        <p class="pesan-sukses">
            Supplier berhasil dihapus!
        </p>
    <?php } ?>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'gagal_hapus') { ?>
        <p class="pesan-error">
            Supplier gagal dihapus karena masih digunakan atau terjadi kesalahan!
        </p>
    <?php } ?>


    <div class="form-barang">

        <h3>
            <?= $data_edit ? 'Edit Supplier' : 'Tambah Supplier'; ?>
        </h3>

        <form method="POST" action="supplier.php<?= $data_edit ? '?edit=' . $data_edit['id'] : ''; ?>">

            <?php if ($data_edit) { ?>

                <input type="hidden"
                       name="id"
                       value="<?= $data_edit['id']; ?>">

            <?php } ?>

            <label>Nama Supplier</label>
            <input type="text"
                   name="nama"
                   maxlength="100"
                   value="<?= htmlspecialchars($data_edit['nama'] ?? ''); ?>"
                   placeholder="Masukkan nama supplier"
                   required>

            <label>Nomor Telepon</label>
            <input type="text"
                   name="telp"
                   maxlength="12"
                   value="<?= htmlspecialchars($data_edit['telp'] ?? ''); ?>"
                   placeholder="Masukkan nomor telepon">

            <label>Alamat</label>
            <textarea name="alamat"
                      placeholder="Masukkan alamat supplier"><?= htmlspecialchars($data_edit['alamat'] ?? ''); ?></textarea>

            <button type="submit"
                    name="<?= $data_edit ? 'update' : 'tambah'; ?>">

                <?= $data_edit ? 'Simpan Perubahan' : 'Tambah Supplier'; ?>

            </button>

            <?php if ($data_edit) { ?>
                <a href="supplier.php">Batal Edit</a>
            <?php } ?>

        </form>

    </div>


    <h3>Daftar Supplier</h3>

    <table>

        <tr>
            <th>ID</th>
            <th>Nama Supplier</th>
            <th>Telepon</th>
            <th>Alamat</th>
            <th>Aksi</th>
        </tr>

        <?php while ($data = mysqli_fetch_assoc($data_supplier)) { ?>

        <tr>

            <td><?= $data['id']; ?></td>

            <td><?= htmlspecialchars($data['nama']); ?></td>

            <td><?= htmlspecialchars($data['telp'] ?? ''); ?></td>

            <td><?= htmlspecialchars($data['alamat'] ?? ''); ?></td>

            <td>

                <a href="supplier.php?edit=<?= $data['id']; ?>">
                    Edit
                </a>

                |

                <form method="POST"
                      action="supplier.php"
                      style="display:inline;"
                      onsubmit="return confirm('Yakin ingin menghapus supplier ini?')">

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
