<?php
require_once 'cek_owner.php';
require_once 'koneksi.php';

$pesan = "";

if (isset($_POST['tambah'])) {

    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $nama = trim($_POST['nama']);
    $alamat = trim($_POST['alamat']);
    $np = trim($_POST['np']);
    $level = (int) $_POST['level'];

    if ($username == "" || $password == "" || $nama == "" || !in_array($level, [1, 2], true)) {

        $pesan = "Data yang wajib diisi belum lengkap!";

    } else {

        $cek = mysqli_prepare($koneksi, "
            SELECT id_user FROM `user` WHERE username = ?
        ");

        mysqli_stmt_bind_param($cek, "s", $username);
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);

        if (mysqli_stmt_num_rows($cek) > 0) {

            $pesan = "Username sudah digunakan!";

        } else {

            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $query = mysqli_prepare($koneksi, "
                INSERT INTO `user`
                (username, password, nama, alamat, np, level)
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            mysqli_stmt_bind_param(
                $query,
                "sssssi",
                $username,
                $password_hash,
                $nama,
                $alamat,
                $np,
                $level
            );

            if (mysqli_stmt_execute($query)) {

                header("Location: user.php?status=berhasil");
                exit;

            } else {

                $pesan = "User gagal ditambahkan!";
            }
        }
    }
}

if (isset($_POST['update'])) {

    $id = (int) $_POST['id_user'];
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $nama = trim($_POST['nama']);
    $alamat = trim($_POST['alamat']);
    $np = trim($_POST['np']);
    $level = (int) $_POST['level'];

    if ($username == "" || $nama == "" || !in_array($level, [1, 2], true)) {

        $pesan = "Username, nama, dan level harus diisi!";

    } else {

        $cek = mysqli_prepare($koneksi, "
            SELECT id_user FROM `user`
            WHERE username = ? AND id_user != ?
        ");

        mysqli_stmt_bind_param($cek, "si", $username, $id);
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);

        if (mysqli_stmt_num_rows($cek) > 0) {

            $pesan = "Username sudah digunakan!";

        } else {

            if ($password != "") {

                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                $query = mysqli_prepare($koneksi, "
                    UPDATE `user`
                    SET username = ?, password = ?, nama = ?,
                        alamat = ?, np = ?, level = ?
                    WHERE id_user = ?
                ");

                mysqli_stmt_bind_param(
                    $query,
                    "sssssii",
                    $username,
                    $password_hash,
                    $nama,
                    $alamat,
                    $np,
                    $level,
                    $id
                );

            } else {

                $query = mysqli_prepare($koneksi, "
                    UPDATE `user`
                    SET username = ?, nama = ?, alamat = ?,
                        np = ?, level = ?
                    WHERE id_user = ?
                ");

                mysqli_stmt_bind_param(
                    $query,
                    "ssssii",
                    $username,
                    $nama,
                    $alamat,
                    $np,
                    $level,
                    $id
                );
            }

            if (mysqli_stmt_execute($query)) {

                header("Location: user.php?status=edit");
                exit;

            } else {

                $pesan = "Data user gagal diperbarui!";
            }
        }
    }
}

if (isset($_POST['hapus'])) {

    $id = (int) $_POST['id_user'];

    if (isset($_SESSION['id_user']) && (int) $_SESSION['id_user'] === $id) {

        header("Location: user.php?status=gagal_hapus");
        exit;
    }

    $cek = mysqli_prepare($koneksi, "
        SELECT level FROM `user` WHERE id_user = ?
    ");

    mysqli_stmt_bind_param($cek, "i", $id);
    mysqli_stmt_execute($cek);
    $hasil_cek = mysqli_stmt_get_result($cek);
    $user_hapus = mysqli_fetch_assoc($hasil_cek);

    if (!$user_hapus) {

        header("Location: user.php?status=gagal_hapus");
        exit;
    }

    if ((int) $user_hapus['level'] === 1) {

        $cek_owner = mysqli_query($koneksi, "
            SELECT COUNT(*) AS jumlah FROM `user` WHERE level = 1
        ");

        $jumlah_owner = mysqli_fetch_assoc($cek_owner);

        if ((int) $jumlah_owner['jumlah'] <= 1) {

            header("Location: user.php?status=gagal_hapus");
            exit;
        }
    }

    $query = mysqli_prepare($koneksi, "
        DELETE FROM `user` WHERE id_user = ?
    ");

    mysqli_stmt_bind_param($query, "i", $id);

    if (mysqli_stmt_execute($query)) {

        header("Location: user.php?status=hapus");
        exit;

    } else {

        header("Location: user.php?status=gagal_hapus");
        exit;
    }
}

$data_edit = null;

if (isset($_GET['edit'])) {

    $id_edit = (int) $_GET['edit'];

    $query_edit = mysqli_prepare($koneksi, "
        SELECT * FROM `user` WHERE id_user = ?
    ");

    mysqli_stmt_bind_param($query_edit, "i", $id_edit);
    mysqli_stmt_execute($query_edit);

    $hasil_edit = mysqli_stmt_get_result($query_edit);
    $data_edit = mysqli_fetch_assoc($hasil_edit);

    if (!$data_edit) {
        header("Location: user.php");
        exit;
    }
}

$data_user = mysqli_query($koneksi, "
    SELECT id_user, username, nama, alamat, np, level
    FROM `user`
    ORDER BY id_user ASC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data User</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <h2>Data User</h2>

    <?php if ($pesan != "") { ?>
        <p class="pesan-error">
            <?= htmlspecialchars($pesan); ?>
        </p>
    <?php } ?>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'berhasil') { ?>
        <p class="pesan-sukses">
            User berhasil ditambahkan!
        </p>
    <?php } ?>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'edit') { ?>
        <p class="pesan-sukses">
            User berhasil diperbarui!
        </p>
    <?php } ?>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'hapus') { ?>
        <p class="pesan-sukses">
            User berhasil dihapus!
        </p>
    <?php } ?>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'gagal_hapus') { ?>
        <p class="pesan-error">
            User gagal dihapus! Periksa akun yang akan dihapus.
        </p>
    <?php } ?>


    <div class="form-barang">

        <h3>
            <?= $data_edit ? 'Edit User' : 'Tambah User'; ?>
        </h3>

        <form method="POST" action="user.php<?= $data_edit ? '?edit=' . $data_edit['id_user'] : ''; ?>">

            <?php if ($data_edit) { ?>

                <input type="hidden"
                       name="id_user"
                       value="<?= $data_edit['id_user']; ?>">

            <?php } ?>

            <label>Username</label>
            <input type="text"
                   name="username"
                   maxlength="30"
                   value="<?= htmlspecialchars($data_edit['username'] ?? ''); ?>"
                   placeholder="Masukkan username"
                   required>

            <label>
                Password
                <?= $data_edit ? '(Kosongkan jika tidak diganti)' : ''; ?>
            </label>

            <input type="password"
                   name="password"
                   placeholder="Masukkan password"
                   <?= $data_edit ? '' : 'required'; ?>>

            <label>Nama</label>
            <input type="text"
                   name="nama"
                   maxlength="50"
                   value="<?= htmlspecialchars($data_edit['nama'] ?? ''); ?>"
                   placeholder="Masukkan nama"
                   required>

            <label>Alamat</label>
            <textarea name="alamat"
                      maxlength="150"
                      placeholder="Masukkan alamat"><?= htmlspecialchars($data_edit['alamat'] ?? ''); ?></textarea>

            <label>NP</label>
            <input type="text"
                   name="np"
                   maxlength="20"
                   value="<?= htmlspecialchars($data_edit['np'] ?? ''); ?>"
                   placeholder="Masukkan NP">

            <label>Level</label>
            <select name="level" required>

                <option value="">-- Pilih Level --</option>

                <option value="1"
                    <?= isset($data_edit) && $data_edit['level'] == 1 ? 'selected' : ''; ?>>
                    Owner
                </option>

                <option value="2"
                    <?= isset($data_edit) && $data_edit['level'] == 2 ? 'selected' : ''; ?>>
                    Kasir
                </option>

            </select>

            <button type="submit"
                    name="<?= $data_edit ? 'update' : 'tambah'; ?>">

                <?= $data_edit ? 'Simpan Perubahan' : 'Tambah User'; ?>

            </button>

            <?php if ($data_edit) { ?>
                <a href="user.php">Batal Edit</a>
            <?php } ?>

        </form>

    </div>


    <h3>Daftar User</h3>

    <table>

        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Nama</th>
            <th>Alamat</th>
            <th>NP</th>
            <th>Level</th>
            <th>Aksi</th>
        </tr>

        <?php while ($data = mysqli_fetch_assoc($data_user)) { ?>

        <tr>

            <td><?= $data['id_user']; ?></td>

            <td><?= htmlspecialchars($data['username'] ?? ''); ?></td>

            <td><?= htmlspecialchars($data['nama'] ?? ''); ?></td>

            <td><?= htmlspecialchars($data['alamat'] ?? ''); ?></td>

            <td><?= htmlspecialchars($data['np'] ?? ''); ?></td>

            <td>
                <?= $data['level'] == 1 ? 'Owner' : 'Kasir'; ?>
            </td>

            <td>

                <a href="user.php?edit=<?= $data['id_user']; ?>">
                    Edit
                </a>

                |

                <form method="POST"
                      action="user.php"
                      style="display:inline;"
                      onsubmit="return confirm('Yakin ingin menghapus user ini?')">

                    <input type="hidden"
                           name="id_user"
                           value="<?= $data['id_user']; ?>">

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
