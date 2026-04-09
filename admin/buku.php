<?php
session_start();
include '../config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

if (isset($_POST['add_book'])) {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $penulis = mysqli_real_escape_string($conn, $_POST['penulis']);
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $id_kat = $_POST['id_kategori'];
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']); 

    $nama_file = time() . '_' . $_FILES['gambar']['name'];
    $source = $_FILES['gambar']['tmp_name'];
    $folder = '../assets/image/books/';

    if (move_uploaded_file($source, $folder . $nama_file)) {
        mysqli_query($conn, "INSERT INTO buku (judul, penulis, harga, stok, id_kategori, gambar, deskripsi) 
                             VALUES ('$judul', '$penulis', '$harga', '$stok', '$id_kat', '$nama_file', '$deskripsi')");
    }
    header("Location: buku.php");
    exit;
}

if (isset($_POST['edit_book'])) {
    $id = $_POST['id_buku'];
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $penulis = mysqli_real_escape_string($conn, $_POST['penulis']);
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $id_kat = $_POST['id_kategori'];
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']); 

    if ($_FILES['gambar']['name'] != "") {

        mysqli_query($conn, "UPDATE buku SET judul='$judul', penulis='$penulis', harga='$harga', 
                             stok='$stok', id_kategori='$id_kat', gambar='$nama_file', 
                             deskripsi='$deskripsi' WHERE id_buku='$id'"); 
    } else {
        mysqli_query($conn, "UPDATE buku SET judul='$judul', penulis='$penulis', harga='$harga', 
                             stok='$stok', id_kategori='$id_kat', 
                             deskripsi='$deskripsi' WHERE id_buku='$id'"); 
    }
    header("Location: buku.php");
    exit;
}





if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $query_img = mysqli_query($conn, "SELECT gambar FROM buku WHERE id_buku = '$id'");
    $data_img = mysqli_fetch_assoc($query_img);

    if (!empty($data_img['gambar']) && file_exists('../assets/image/books/' . $data_img['gambar'])) {
        unlink('../assets/image/books/' . $data_img['gambar']);
    }

    mysqli_query($conn, "DELETE FROM cart WHERE id_buku = '$id'");

  
    mysqli_query($conn, "DELETE FROM buku WHERE id_buku = '$id'");

    header("Location: buku.php");
    exit;
}

$search = '';
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $buku_list = mysqli_query($conn, "SELECT buku.*, kategori.nama_kategori FROM buku 
                                       LEFT JOIN kategori ON buku.id_kategori = kategori.id_kategori 
                                       WHERE buku.judul LIKE '%$search%' OR buku.penulis LIKE '%$search%'
                                       ORDER BY buku.id_buku DESC");
} else {
    $buku_list = mysqli_query($conn, "SELECT buku.*, kategori.nama_kategori FROM buku 
                                       LEFT JOIN kategori ON buku.id_kategori = kategori.id_kategori 
                                       ORDER BY buku.id_buku DESC");
}



$kategori_query = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Data Buku | Admin M&N</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin/buku.css">
</head>

<body>
    <?php include '../include/sidebar_admin.php'; ?>

    <main class="main-content">
        <div class="header-section">
            <h1>Kelola <span>Data Buku</span></h1>
            <div class="search-section">
                <form method="GET" action="" class="search-box">
                    <input type="text" name="search" placeholder="Cari judul atau penulis..."
                        value="<?= htmlspecialchars($search) ?>">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
                <?php if (!empty($search)): ?>
                    <a href="buku.php" class="clear-search"><i class="fas fa-times"></i> Clear</a>
                <?php endif; ?>
                <button class="btn-add-modal" onclick="openAddModal()">
                    <i class="fas fa-plus-circle"></i> Tambah Buku
                </button>
            </div>
        </div>

        <?php if (!empty($search)): ?>
            <div class="search-stats">
                <span><i class="fas fa-search"></i> Hasil pencarian: "<?= htmlspecialchars($search) ?>"</span>
                <span><?= mysqli_num_rows($buku_list) ?> buku ditemukan</span>
            </div>
        <?php endif; ?>

        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>Cover</th>
                        <th>Buku</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Deskripsi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($buku_list) > 0): ?>
                        <?php while ($b = mysqli_fetch_assoc($buku_list)): ?>
                            <tr>
                                <td><img src="../assets/image/books/<?= $b['gambar']; ?>" class="img-preview"></td>
                                <td><b><?= $b['judul']; ?></b><br><small
                                        style="color:var(--text-dim)"><?= $b['penulis']; ?></small></td>
                                <td><?= $b['nama_kategori']; ?></td>
                                <td>Rp <?= number_format($b['harga'], 0, ',', '.'); ?></td>
                                <td><?= $b['stok']; ?></td>
                                <td style="max-width:200px;">
                                    <small style="color:var(--text-dim); display:-webkit-box; -webkit-line-clamp:2; 
    -                                   webkit-box-orient:vertical; overflow:hidden; line-height:1.5;">
                                        <?= !empty($b['deskripsi']) ? htmlspecialchars($b['deskripsi']) : '<i>-</i>' ?>
                                    </small>
                                </td>
                                <td class="action-icons">
                                    <a href="javascript:void(0)" onclick="openEdit(<?= htmlspecialchars(json_encode($b)); ?>)"
                                        style="color:#f59e0b;"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <a href="?delete=<?= $b['id_buku']; ?>" onclick="return confirm('Yakin hapus buku ini?')"
                                        style="color:#ef4444;"><i class="fa-solid fa-trash-can"></i></a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr class="empty-row">
                            <td colspan="6">
                                <i class="fas fa-book-open fa-2x"
                                    style="opacity:0.5; margin-bottom:10px; display:block;"></i>
                                <?php if (!empty($search)): ?>
                                    Tidak ada buku dengan kata kunci "<?= htmlspecialchars($search) ?>"
                                <?php else: ?>
                                    Belum ada data buku. Silakan tambah buku baru.
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- MODAL TAMBAH BUKU -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-book"></i> Tambah <span>Buku Baru</span></h3>
                <span class="close-modal" onclick="closeAddModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="input-group">
                        <label><i class="fas fa-heading"></i> Judul Buku</label>
                        <input type="text" name="judul" placeholder="Masukkan judul buku..." required>
                    </div>
                    <div class="input-group">
                        <label><i class="fas fa-user"></i> Penulis</label>
                        <input type="text" name="penulis" placeholder="Nama penulis..." required>
                    </div>
                    <div class="form-row">
                        <div class="input-group">
                            <label><i class="fas fa-tag"></i> Harga (Rp)</label>
                            <input type="number" name="harga" placeholder="Harga" required>
                        </div>
                        <div class="input-group">
                            <label><i class="fas fa-boxes"></i> Stok</label>
                            <input type="number" name="stok" placeholder="Jumlah stok" required>
                        </div>
                        <div class="input-group">
                            <label><i class="fas fa-align-left"></i> Deskripsi Buku</label>
                            <textarea name="deskripsi" placeholder="Tulis deskripsi buku..." style="background:rgba(255,255,255,0.07); border:1px solid var(--glass-border); 
        padding:14px 16px; border-radius:14px; color:white; outline:none; 
        font-size:0.9rem; resize:vertical; min-height:100px; font-family:'Inter',sans-serif;"></textarea>
                        </div>
                    </div>
                    <div class="input-group">
                        <label><i class="fas fa-layer-group"></i> Kategori</label>
                        <select name="id_kategori" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php mysqli_data_seek($kategori_query, 0);
                            while ($k = mysqli_fetch_assoc($kategori_query)): ?>
                                <option value="<?= $k['id_kategori']; ?>"><?= $k['nama_kategori']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="input-group">
                        <label><i class="fas fa-image"></i> Cover Buku</label>
                        <input type="file" name="gambar" accept="image/*" required>
                        <small style="color:var(--text-dim);">Format: JPG, PNG. Maks 2MB</small>
                    </div>
                    <button type="submit" name="add_book" class="btn-submit">
                        <i class="fas fa-save"></i> Simpan Buku
                    </button>
                    <button type="button" class="btn-cancel" onclick="closeAddModal()">
                        <i class="fas fa-times"></i> Batal
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT BUKU -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-edit"></i> Edit <span>Buku</span></h3>
                <span class="close-modal" onclick="closeEditModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_buku" id="eid">
                    <div class="input-group">
                        <label>Judul Buku</label>
                        <input type="text" name="judul" id="ejudul" required>
                    </div>
                    <div class="input-group">
                        <label>Penulis</label>
                        <input type="text" name="penulis" id="epenulis" required>
                    </div>
                    <div class="form-row">
                        <div class="input-group">
                            <label>Harga (Rp)</label>
                            <input type="number" name="harga" id="eharga" required>
                        </div>
                        <div class="input-group">
                            <label>Stok</label>
                            <input type="number" name="stok" id="estok" required>
                        </div>
                    </div>
                    <div class="input-group">
                        <label><i class="fas fa-align-left"></i> Deskripsi Buku</label>
                        <textarea name="deskripsi" id="edeskripsi" placeholder="Tulis deskripsi buku..."
                            style="background:rgba(255,255,255,0.07); border:1px solid var(--glass-border); 
                        padding:14px 16px; border-radius:14px; color:white; outline:none; 
                         font-size:0.9rem; resize:vertical; min-height:100px; font-family:'Inter',sans-serif;"></textarea>
                    </div>
                    <div class="input-group">
                        <label>Kategori</label>
                        <select name="id_kategori" id="ekat">
                            <?php mysqli_data_seek($kategori_query, 0);
                            while ($k = mysqli_fetch_assoc($kategori_query)): ?>
                                <option value="<?= $k['id_kategori']; ?>"><?= $k['nama_kategori']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>Ganti Cover (Opsional)</label>
                        <input type="file" name="gambar" accept="image/*">
                        <small style="color:var(--text-dim);">Kosongkan jika tidak ingin mengubah cover</small>
                    </div>
                    <button type="submit" name="edit_book" class="btn-submit"
                        style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                        <i class="fas fa-sync-alt"></i> Update Buku
                    </button>
                    <button type="button" class="btn-cancel" onclick="closeEditModal()">
                        <i class="fas fa-times"></i> Batal
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('addModal').style.display = 'flex';
        }
        function closeAddModal() {
            document.getElementById('addModal').style.display = 'none';
        }
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        function openEdit(d) {
            document.getElementById('eid').value = d.id_buku;
            document.getElementById('ejudul').value = d.judul;
            document.getElementById('epenulis').value = d.penulis;
            document.getElementById('eharga').value = d.harga;
            document.getElementById('estok').value = d.stok;
            document.getElementById('ekat').value = d.id_kategori;
            document.getElementById('edeskripsi').value = d.deskripsi ?? ''; // ← tambah ini
            document.getElementById('editModal').style.display = 'flex';
        }
        window.onclick = function (e) {
            let addModal = document.getElementById('addModal');
            let editModal = document.getElementById('editModal');
            if (e.target === addModal) closeAddModal();
            if (e.target === editModal) closeEditModal();
        }
    </script>
</body>

</html>