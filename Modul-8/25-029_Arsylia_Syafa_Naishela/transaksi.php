
<?php
require_once 'cek_login.php';
require_once 'koneksi.php';

$pesan = "";

if (isset($_POST['simpan']) || isset($_POST['edit'])) {

    $pelanggan_id = $_POST['pelanggan_id'];
    $keterangan = $_POST['keterangan'];
    $barang_id = $_POST['barang_id'];
    $qty = $_POST['qty'];

    $jumlah_barang = count($barang_id);

    $cek_pelanggan = mysqli_prepare($koneksi,
        "SELECT id FROM pelanggan WHERE id=?"
    );
    mysqli_stmt_bind_param($cek_pelanggan, "s", $pelanggan_id);
    mysqli_stmt_execute($cek_pelanggan);
    mysqli_stmt_store_result($cek_pelanggan);

    if (mysqli_stmt_num_rows($cek_pelanggan) == 0) {
        $pesan = "Pelanggan tidak ditemukan!";
    } elseif ($jumlah_barang == 0 || $jumlah_barang != count($qty)) {
        $pesan = "Pilih minimal satu barang!";
    } else {

        if (count(array_unique($barang_id)) != $jumlah_barang) {
            $pesan = "Barang yang sama tidak boleh dipilih dua kali!";
        } else {

            mysqli_begin_transaction($koneksi);

            try {
                $total = 0;

                if (isset($_POST['edit'])) {

                    $transaksi_id = (int)$_POST['transaksi_id'];

                    $cek_transaksi = mysqli_prepare($koneksi,
                        "SELECT id FROM transaksi WHERE id=? FOR UPDATE"
                    );
                    mysqli_stmt_bind_param($cek_transaksi, "i", $transaksi_id);
                    mysqli_stmt_execute($cek_transaksi);
                    mysqli_stmt_store_result($cek_transaksi);

                    if (mysqli_stmt_num_rows($cek_transaksi) == 0) {
                        throw new Exception("Transaksi tidak ditemukan!");
                    }

                    $detail_lama = mysqli_query($koneksi,
                        "SELECT barang_id, qty FROM detail_transaksi
                         WHERE transaksi_id=$transaksi_id FOR UPDATE"
                    );

                    while ($lama = mysqli_fetch_assoc($detail_lama)) {
                        $update_stok = mysqli_prepare($koneksi,
                            "UPDATE barang SET stok = stok + ? WHERE id=?"
                        );

                        mysqli_stmt_bind_param($update_stok, "ii",
                            $lama['qty'], $lama['barang_id']
                        );
                        mysqli_stmt_execute($update_stok);
                        mysqli_stmt_close($update_stok);
                    }

                    $hapus_detail = mysqli_prepare($koneksi,
                        "DELETE FROM detail_transaksi WHERE transaksi_id=?"
                    );
                    mysqli_stmt_bind_param($hapus_detail, "i", $transaksi_id);
                    mysqli_stmt_execute($hapus_detail);
                    mysqli_stmt_close($hapus_detail);

                } else {
                    $query = mysqli_prepare($koneksi,
                        "INSERT INTO transaksi
                         (waktu_transaksi, keterangan, total, pelanggan_id)
                         VALUES (CURDATE(), ?, 0, ?)"
                    );

                    mysqli_stmt_bind_param($query, "ss",
                        $keterangan, $pelanggan_id
                    );
                    mysqli_stmt_execute($query);

                    $transaksi_id = mysqli_insert_id($koneksi);
                }

                for ($i = 0; $i < $jumlah_barang; $i++) {

                    $id_barang = (int)$barang_id[$i];
                    $jumlah = (int)$qty[$i];

                    if ($id_barang <= 0 || $jumlah <= 0) {
                        throw new Exception("Barang atau jumlah tidak valid!");
                    }

                    $cek_barang = mysqli_prepare($koneksi,
                        "SELECT harga, stok FROM barang
                         WHERE id=? FOR UPDATE"
                    );

                    mysqli_stmt_bind_param($cek_barang, "i", $id_barang);
                    mysqli_stmt_execute($cek_barang);
                    mysqli_stmt_bind_result($cek_barang, $harga, $stok);

                    if (!mysqli_stmt_fetch($cek_barang)) {
                        mysqli_stmt_close($cek_barang);
                        throw new Exception("Barang tidak ditemukan!");
                    }

                    mysqli_stmt_close($cek_barang);

                    if ($jumlah > $stok) {
                        throw new Exception("Stok barang tidak mencukupi!");
                    }

                    $total += $harga * $jumlah;

                    $detail = mysqli_prepare($koneksi,
                        "INSERT INTO detail_transaksi
                         (transaksi_id, barang_id, harga, qty)
                         VALUES (?, ?, ?, ?)"
                    );

                    mysqli_stmt_bind_param($detail, "iiii",
                        $transaksi_id, $id_barang, $harga, $jumlah
                    );
                    mysqli_stmt_execute($detail);
                    mysqli_stmt_close($detail);

                    $update_stok = mysqli_prepare($koneksi,
                        "UPDATE barang SET stok = stok - ?
                         WHERE id=? AND stok >= ?"
                    );

                    mysqli_stmt_bind_param($update_stok, "iii",
                        $jumlah, $id_barang, $jumlah
                    );
                    mysqli_stmt_execute($update_stok);

                    if (mysqli_stmt_affected_rows($update_stok) == 0) {
                        mysqli_stmt_close($update_stok);
                        throw new Exception("Gagal mengurangi stok barang!");
                    }

                    mysqli_stmt_close($update_stok);
                }

                if (isset($_POST['edit'])) {
                    $update_total = mysqli_prepare($koneksi,
                        "UPDATE transaksi
                         SET pelanggan_id=?, keterangan=?, total=?
                         WHERE id=?"
                    );

                    mysqli_stmt_bind_param($update_total, "ssii",
                        $pelanggan_id, $keterangan, $total, $transaksi_id
                    );
                } else {
                    $update_total = mysqli_prepare($koneksi,
                        "UPDATE transaksi SET total=? WHERE id=?"
                    );

                    mysqli_stmt_bind_param($update_total, "ii",
                        $total, $transaksi_id
                    );
                }

                mysqli_stmt_execute($update_total);
                mysqli_stmt_close($update_total);

                mysqli_commit($koneksi);

                if (isset($_POST['edit'])) {
                    header("Location: transaksi.php?status=edit");
                } else {
                    header("Location: transaksi.php?status=berhasil");
                }
                exit;

            } catch (Exception $e) {
                mysqli_rollback($koneksi);
                $pesan = "Transaksi gagal: " . $e->getMessage();
            }
        }
    }
}

if (isset($_POST['hapus'])) {

    $transaksi_id = (int)$_POST['transaksi_id'];

    mysqli_begin_transaction($koneksi);

    try {
        $cek = mysqli_prepare($koneksi,
            "SELECT id FROM transaksi WHERE id=? FOR UPDATE"
        );
        mysqli_stmt_bind_param($cek, "i", $transaksi_id);
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);

        if (mysqli_stmt_num_rows($cek) == 0) {
            throw new Exception("Transaksi tidak ditemukan!");
        }

        $detail = mysqli_query($koneksi,
            "SELECT barang_id, qty FROM detail_transaksi
             WHERE transaksi_id=$transaksi_id FOR UPDATE"
        );

        while ($d = mysqli_fetch_assoc($detail)) {
            $update_stok = mysqli_prepare($koneksi,
                "UPDATE barang SET stok = stok + ? WHERE id=?"
            );

            mysqli_stmt_bind_param($update_stok, "ii",
                $d['qty'], $d['barang_id']
            );
            mysqli_stmt_execute($update_stok);
            mysqli_stmt_close($update_stok);
        }

        $hapus_detail = mysqli_prepare($koneksi,
            "DELETE FROM detail_transaksi WHERE transaksi_id=?"
        );
        mysqli_stmt_bind_param($hapus_detail, "i", $transaksi_id);
        mysqli_stmt_execute($hapus_detail);
        mysqli_stmt_close($hapus_detail);

        $hapus = mysqli_prepare($koneksi,
            "DELETE FROM transaksi WHERE id=?"
        );
        mysqli_stmt_bind_param($hapus, "i", $transaksi_id);
        mysqli_stmt_execute($hapus);
        mysqli_stmt_close($hapus);

        mysqli_commit($koneksi);

        header("Location: transaksi.php?status=hapus");
        exit;

    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        $pesan = "Gagal menghapus transaksi: " . $e->getMessage();
    }
}

$data_edit = null;
$detail_edit = [];

if (isset($_GET['edit'])) {

    $id_edit = (int)$_GET['edit'];

    $query_edit = mysqli_prepare($koneksi,
        "SELECT * FROM transaksi WHERE id=?"
    );
    mysqli_stmt_bind_param($query_edit, "i", $id_edit);
    mysqli_stmt_execute($query_edit);

    $hasil_edit = mysqli_stmt_get_result($query_edit);
    $data_edit = mysqli_fetch_assoc($hasil_edit);

    if ($data_edit) {
        $hasil_detail = mysqli_query($koneksi,
            "SELECT * FROM detail_transaksi
             WHERE transaksi_id=$id_edit"
        );

        while ($d = mysqli_fetch_assoc($hasil_detail)) {
            $detail_edit[] = $d;
        }
    }
}

$daftar_barang = mysqli_query($koneksi,
    "SELECT * FROM barang ORDER BY nama_barang ASC"
);
$barang_list = [];

while ($b = mysqli_fetch_assoc($daftar_barang)) {
    $barang_list[] = $b;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Transaksi Penjualan</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h2>Transaksi Penjualan</h2>

<?php
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'berhasil') {
        echo "<p>Transaksi berhasil disimpan!</p>";
    } elseif ($_GET['status'] == 'edit') {
        echo "<p>Transaksi berhasil diedit!</p>";
    } elseif ($_GET['status'] == 'hapus') {
        echo "<p>Transaksi berhasil dihapus!</p>";
    }
}

if ($pesan != "") {
    echo "<p>" . htmlspecialchars($pesan) . "</p>";
}
?>

<h3><?php echo $data_edit ? 'Edit Transaksi' : 'Tambah Transaksi'; ?></h3>

<form method="POST" action="transaksi.php">

    <?php if ($data_edit) { ?>
        <input type="hidden" name="transaksi_id"
            value="<?php echo $data_edit['id']; ?>">
    <?php } ?>

    <label>Pilih Pelanggan</label><br>

    <select name="pelanggan_id" required>
        <option value="">-- Pilih Pelanggan --</option>

        <?php
        $pelanggan = mysqli_query($koneksi, "SELECT * FROM pelanggan");

        while ($p = mysqli_fetch_assoc($pelanggan)) {
        ?>
            <option value="<?php echo htmlspecialchars($p['id']); ?>"
                <?php
                if ($data_edit && $data_edit['pelanggan_id'] == $p['id']) {
                    echo "selected";
                }
                ?>>
                <?php echo htmlspecialchars($p['nama']); ?>
            </option>
        <?php } ?>

    </select>

    <br><br>

    <label>Keterangan</label><br>
    <textarea name="keterangan"><?php
        echo htmlspecialchars($data_edit['keterangan'] ?? '');
    ?></textarea>

    <h3>Daftar Barang</h3>

    <div id="daftarBarang">

        <?php
        if ($data_edit && count($detail_edit) > 0) {
            foreach ($detail_edit as $d) {
        ?>

        <div class="baris-barang">

            <select name="barang_id[]" required>
                <option value="">-- Pilih Barang --</option>

                <?php foreach ($barang_list as $b) { ?>
                    <option value="<?php echo $b['id']; ?>"
                        <?php echo $d['barang_id'] == $b['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($b['nama_barang']); ?>
                        - Rp <?php echo number_format($b['harga']); ?>
                        (Stok: <?php echo $b['stok']; ?>)
                    </option>
                <?php } ?>

            </select>

            <input type="number" name="qty[]" min="1"
                value="<?php echo $d['qty']; ?>" required>

            <button type="button" onclick="hapusBaris(this)">Hapus</button>

        </div>

        <?php
            }
        } else {
        ?>

        <div class="baris-barang">

            <select name="barang_id[]" required>
                <option value="">-- Pilih Barang --</option>

                <?php foreach ($barang_list as $b) { ?>
                    <option value="<?php echo $b['id']; ?>">
                        <?php echo htmlspecialchars($b['nama_barang']); ?>
                        - Rp <?php echo number_format($b['harga']); ?>
                        (Stok: <?php echo $b['stok']; ?>)
                    </option>
                <?php } ?>

            </select>

            <input type="number" name="qty[]" min="1"
                placeholder="Jumlah" required>

            <button type="button" onclick="hapusBaris(this)">Hapus</button>

        </div>

        <?php } ?>

    </div>

    <br>

    <button type="button" onclick="tambahBarang()">+ Tambah Barang</button>

    <br><br>

    <?php if ($data_edit) { ?>
        <button type="submit" name="edit">Simpan Perubahan</button>
        <a href="transaksi.php">Batal</a>
    <?php } else { ?>
        <button type="submit" name="simpan">Simpan Transaksi</button>
    <?php } ?>

</form>

<hr>

<h2>Riwayat Transaksi Penjualan</h2>

<table border="1" cellpadding="8" cellspacing="0">

    <tr>
        <th>ID Transaksi</th>
        <th>Tanggal</th>
        <th>Pelanggan</th>
        <th>Barang yang Dibeli</th>
        <th>Total</th>
        <th>Keterangan</th>
        <th>Aksi</th>
    </tr>

    <?php
    $riwayat = mysqli_query($koneksi, "
        SELECT transaksi.*, pelanggan.nama
        FROM transaksi
        LEFT JOIN pelanggan ON transaksi.pelanggan_id = pelanggan.id
        ORDER BY transaksi.id DESC
    ");

    while ($t = mysqli_fetch_assoc($riwayat)) {
    ?>

    <tr>
        <td><?php echo $t['id']; ?></td>
        <td><?php echo $t['waktu_transaksi']; ?></td>
        <td><?php echo htmlspecialchars($t['nama'] ?? '-'); ?></td>

        <td>
            <?php
            $id_transaksi = (int)$t['id'];

            $detail = mysqli_query($koneksi, "
                SELECT barang.nama_barang, detail_transaksi.harga,
                       detail_transaksi.qty
                FROM detail_transaksi
                JOIN barang ON detail_transaksi.barang_id = barang.id
                WHERE detail_transaksi.transaksi_id = $id_transaksi
            ");

            while ($d = mysqli_fetch_assoc($detail)) {
                echo htmlspecialchars($d['nama_barang']);
                echo " (" . $d['qty'] . " x Rp ";
                echo number_format($d['harga']) . ")<br>";
            }
            ?>
        </td>

        <td>Rp <?php echo number_format($t['total']); ?></td>
        <td><?php echo htmlspecialchars($t['keterangan'] ?? '-'); ?></td>

        <td>
            <a href="transaksi.php?edit=<?php echo $t['id']; ?>">Edit</a>

            <form method="POST" action="transaksi.php"
                style="display:inline;"
                onsubmit="return confirm('Yakin ingin menghapus transaksi ini? Stok barang akan dikembalikan.');">

                <input type="hidden" name="transaksi_id"
                    value="<?php echo $t['id']; ?>">

                <button type="submit" name="hapus">Hapus</button>

            </form>
        </td>
    </tr>

    <?php } ?>

</table>

<br>

<a href="index.php">Kembali</a>

<script>
function tambahBarang() {
    var daftar = document.getElementById("daftarBarang");
    var baris = daftar.querySelector(".baris-barang").cloneNode(true);

    baris.querySelector("select").selectedIndex = 0;
    baris.querySelector("input").value = "";

    daftar.appendChild(baris);
}

function hapusBaris(tombol) {
    var daftar = document.getElementById("daftarBarang");

    if (daftar.querySelectorAll(".baris-barang").length > 1) {
        tombol.parentElement.remove();
    } else {
        alert("Minimal harus ada satu barang!");
    }
}
</script>

</body>
</html>
