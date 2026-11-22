<?php
require_once 'cek_owner.php';
require_once 'koneksi.php';

if (isset($_POST['tambah'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $telp = $_POST['telp'];
    $alamat = $_POST['alamat'];

    $query = mysqli_prepare($koneksi, "INSERT INTO pelanggan (id, nama, jenis_kelamin, telp, alamat) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($query, "sssss", $id, $nama, $jenis_kelamin, $telp, $alamat);

    if (mysqli_stmt_execute($query)) {
        header("Location: pelanggan.php?status=berhasil");
    } else {
        echo "Gagal menambahkan pelanggan: " . mysqli_error($koneksi);
    }
    exit;
}

if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $telp = $_POST['telp'];
    $alamat = $_POST['alamat'];

    $query = mysqli_prepare($koneksi, "UPDATE pelanggan SET nama=?, jenis_kelamin=?, telp=?, alamat=? WHERE id=?");
    mysqli_stmt_bind_param($query, "sssss", $nama, $jenis_kelamin, $telp, $alamat, $id);

    if (mysqli_stmt_execute($query)) {
        header("Location: pelanggan.php?status=edit");
    } else {
        echo "Gagal mengedit pelanggan: " . mysqli_error($koneksi);
    }
    exit;
}

if (isset($_POST['hapus'])) {
    $id = $_POST['id'];

    $query = mysqli_prepare($koneksi, "DELETE FROM pelanggan WHERE id=?");
    mysqli_stmt_bind_param($query, "s", $id);

    if (mysqli_stmt_execute($query)) {
        header("Location: pelanggan.php?status=hapus");
    } else {
        header("Location: pelanggan.php?status=gagal_hapus");
    }
    exit;
}

$data_edit = null;

if (isset($_GET['edit'])) {
    $id = $_GET['edit'];

    $query = mysqli_prepare($koneksi, "SELECT * FROM pelanggan WHERE id=?");
    mysqli_stmt_bind_param($query, "s", $id);
    mysqli_stmt_execute($query);

    $hasil_edit = mysqli_stmt_get_result($query);
    $data_edit = mysqli_fetch_assoc($hasil_edit);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Pelanggan</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h2>Data Pelanggan</h2>

<?php
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'berhasil') {
        echo "<p>Data pelanggan berhasil ditambahkan!</p>";
    } elseif ($_GET['status'] == 'edit') {
        echo "<p>Data pelanggan berhasil diedit!</p>";
    } elseif ($_GET['status'] == 'hapus') {
        echo "<p>Data pelanggan berhasil dihapus!</p>";
    } elseif ($_GET['status'] == 'gagal_hapus') {
        echo "<p>Data pelanggan gagal dihapus!</p>";
    }
}
?>

<h3><?php echo $data_edit ? 'Edit Pelanggan' : 'Tambah Pelanggan'; ?></h3>

<form method="POST" action="pelanggan.php">

    <label>ID Pelanggan</label><br>
    <input type="text" name="id"
        value="<?php echo htmlspecialchars($data_edit['id'] ?? ''); ?>"
        <?php echo $data_edit ? 'readonly' : 'required'; ?>><br><br>

    <label>Nama Pelanggan</label><br>
    <input type="text" name="nama"
        value="<?php echo htmlspecialchars($data_edit['nama'] ?? ''); ?>"
        required><br><br>

    <label>Jenis Kelamin</label><br>
    <select name="jenis_kelamin" required>
        <option value="">-- Pilih Jenis Kelamin --</option>
        <option value="L" <?php echo (isset($data_edit['jenis_kelamin']) && $data_edit['jenis_kelamin'] == 'L') ? 'selected' : ''; ?>>Laki-laki</option>
        <option value="P" <?php echo (isset($data_edit['jenis_kelamin']) && $data_edit['jenis_kelamin'] == 'P') ? 'selected' : ''; ?>>Perempuan</option>
    </select><br><br>

    <label>No. Telepon</label><br>
    <input type="text" name="telp"
        value="<?php echo htmlspecialchars($data_edit['telp'] ?? ''); ?>"><br><br>

    <label>Alamat</label><br>
    <textarea name="alamat"><?php echo htmlspecialchars($data_edit['alamat'] ?? ''); ?></textarea><br><br>

    <?php if ($data_edit) { ?>
        <button type="submit" name="edit">Simpan Perubahan</button>
        <a href="pelanggan.php">Batal</a>
    <?php } else { ?>
        <button type="submit" name="tambah">Tambah Pelanggan</button>
    <?php } ?>

</form>

<hr>

<h3>Daftar Pelanggan</h3>

<table border="1" cellpadding="8">
    <tr>
        <th>ID Pelanggan</th>
        <th>Nama</th>
        <th>Jenis Kelamin</th>
        <th>No. Telepon</th>
        <th>Alamat</th>
        <th>Aksi</th>
    </tr>

    <?php
    $data = mysqli_query($koneksi, "SELECT * FROM pelanggan");

    while ($row = mysqli_fetch_assoc($data)) {
    ?>
        <tr>
            <td><?php echo htmlspecialchars($row['id']); ?></td>
            <td><?php echo htmlspecialchars($row['nama']); ?></td>
            <td><?php echo $row['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?></td>
            <td><?php echo htmlspecialchars($row['telp']); ?></td>
            <td><?php echo htmlspecialchars($row['alamat']); ?></td>
            <td>
                <a href="pelanggan.php?edit=<?php echo urlencode($row['id']); ?>">Edit</a>

                <form method="POST" action="pelanggan.php" style="display:inline;"
                    onsubmit="return confirm('Yakin ingin menghapus pelanggan ini?');">
                    <input type="hidden" name="id"
                        value="<?php echo htmlspecialchars($row['id']); ?>">
                    <button type="submit" name="hapus">Hapus</button>
                </form>
            </td>
        </tr>
    <?php } ?>

</table>

<br>
<a href="index.php">Kembali ke Menu Utama</a>

</body>
</html>
