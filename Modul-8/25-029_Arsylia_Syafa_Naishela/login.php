<?php
session_start();

if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Sistem Penjualan</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <h2>Login Sistem Penjualan</h2>

    <form action="proses_login.php" method="POST">

        <label>Username</label>
        <br>
        <input type="text" name="username" required>

        <br><br>

        <label>Password</label>
        <br>
        <input type="password" name="password" required>

        <br><br>

        <button type="submit" name="login">Login</button>

    </form>

    <?php
    if (isset($_GET['pesan'])) {
        if ($_GET['pesan'] == 'gagal') {
            echo "<p>Username atau password salah!</p>";
        }
    }
    ?>

</body>
</html>
