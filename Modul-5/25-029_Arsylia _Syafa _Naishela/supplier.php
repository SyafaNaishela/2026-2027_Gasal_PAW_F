<?php
include 'koneksi.php';

$query = mysqli_query($koneksi, "SELECT * FROM supplier ORDER BY id ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Data Supplier</title>
</head>
<body>

<h2>Data Supplier</h2>

<a href="tambah_supplier.php">+ Tambah Supplier</a>
<br><br>

<table border="1" cellpadding="10" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Nama Supplier</th>
        <th>Telepon</th>
        <th>Alamat</th>
        <th>Aksi</th>
    </tr>

    <?php while ($data = mysqli_fetch_assoc($query)) { ?>
    <tr>
        <td><?= $data['id']; ?></td>
        <td><?= htmlspecialchars($data['nama']); ?></td>
        <td><?= htmlspecialchars($data['telp']); ?></td>
        <td><?= htmlspecialchars($data['alamat']); ?></td>
        <td>
            <a href="edit_supplier.php?id=<?= $data['id']; ?>">Edit</a> |
            <a href="hapus_supplier.php?id=<?= $data['id']; ?>"
               onclick="return confirm('Yakin ingin menghapus supplier ini?')">
               Hapus
            </a>
        </td>
    </tr>
    <?php } ?>

</table>

</body>
</html>