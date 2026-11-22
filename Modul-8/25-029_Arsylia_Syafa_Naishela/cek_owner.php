<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

if ((int) $_SESSION['level'] !== 1) {
    echo "<script>
        alert('Maaf, Anda tidak memiliki akses ke halaman ini!');
        window.location.href = 'index.php';
    </script>";
    exit;
}
?>
