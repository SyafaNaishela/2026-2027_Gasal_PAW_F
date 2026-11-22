<?php
session_start();
include 'koneksi.php';

$username = $_POST['username'];
$password = $_POST['password'];

$query = mysqli_prepare($koneksi, "
    SELECT * FROM user WHERE username = ?
");

mysqli_stmt_bind_param($query, "s", $username);
mysqli_stmt_execute($query);

$result = mysqli_stmt_get_result($query);
$data = mysqli_fetch_assoc($result);

if ($data && password_verify($password, $data['password'])) {

    session_regenerate_id(true);

    $_SESSION['login'] = true;
    $_SESSION['id_user'] = $data['id_user'];
    $_SESSION['username'] = $data['username'];
    $_SESSION['nama'] = $data['nama'];
    $_SESSION['level'] = $data['level'];

    header("Location: index.php");
    exit;

} else {

    header("Location: login.php?pesan=gagal");
    exit;
}
?>
