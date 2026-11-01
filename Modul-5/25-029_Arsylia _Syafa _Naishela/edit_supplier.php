<?php
include 'koneksi.php';

$id = (int) ($_GET['id'] ?? 0);

$query = mysqli_prepare(
    $koneksi,
    "SELECT * FROM supplier WHERE id = ?"
);

mysqli_stmt_bind_param($query, "i", $id);
mysqli_stmt_execute($query);

$result = mysqli_stmt_get_result($query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    die("Data supplier tidak ditemukan!");
}

if (isset($_POST['update'])) {
    $nama = $_POST['nama'];
    $telp = $_POST['telp'];
    $alamat = $_POST['alamat'];

    $update = mysqli_prepare(
        $koneksi,
        "UPDATE supplier SET nama=?, telp=?, alamat=?
         WHERE id=?"
    );

    mysqli_stmt_bind_param(
        $update, "sssi", $nama, $telp, $alamat, $id
    );

    if (mysqli_stmt_execute($update)) {
        header("Location: supplier.php");
        exit;
    } else {
        echo "Gagal mengedit supplier!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Edit Supplier</title>
</head>
<body>

<h2>Edit Data Supplier</h2>

<form method="POST">
    <p>
        Nama Supplier:<br>
        <input type="text" name="nama"
        value="<?= htmlspecialchars($data['nama']); ?>" required>
    </p>

    <p>
        Telepon:<br>
        <input type="text" name="telp"
        value="<?= htmlspecialchars($data['telp']); ?>" required>
    </p>

    <p>
        Alamat:<br>
        <textarea name="alamat" required><?= htmlspecialchars($data['alamat']); ?></textarea>
    </p>

    <button type="submit" name="update">Update</button>
    <a href="supplier.php">Batal</a>
</form>

</body>
</html>