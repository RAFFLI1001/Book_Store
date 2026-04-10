<?php
session_start();
include '../config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { 
    header("Location: ../login.php"); 
    exit; 
}


if (isset($_POST['add_category'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_kategori']);
    mysqli_query($conn, "INSERT INTO kategori (nama_kategori) VALUES ('$nama')");
    header("Location: kategori.php");
}


if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $cek = mysqli_query($conn, "SELECT * FROM buku WHERE id_kategori = '$id'");

    if (mysqli_num_rows($cek) > 0) {
        echo "<script>
            alert('Kategori tidak bisa dihapus karena masih ada buku di dalamnya!');
            window.location='kategori.php';
        </script>";
    } else {
        mysqli_query($conn, "DELETE FROM kategori WHERE id_kategori = '$id'");
        header("Location: kategori.php");
    }
}

if (isset($_POST['edit_category'])) {
    $id = $_POST['id_kategori'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama_kategori']);
    mysqli_query($conn, "UPDATE kategori SET nama_kategori = '$nama' WHERE id_kategori = '$id'");
    header("Location: kategori.php");
}

$result = mysqli_query($conn, "SELECT * FROM kategori ORDER BY id_kategori DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kategori | Admin M&N</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin/kategori.css">
</head>
<body>

   <?php include '../include/sidebar_admin.php'; ?>

    <main class="main-content">
        <div class="header-flex">
            <h1>Kelola <span>Kategori</span></h1>
            <div class="stats-badge"><p style="color: var(--text-dim);">Total: <b><?= mysqli_num_rows($result); ?></b> Kategori</p></div>
        </div>

        <div class="card-form">
            <?php if(isset($_GET['edit'])): 
                $id_edit = $_GET['edit'];
                $data_edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM kategori WHERE id_kategori = '$id_edit'"));
            ?>
                <h4 style="margin-bottom: 15px; color: var(--accent-purple);">Mode Edit Kategori</h4>
                <form action="" method="POST" class="form-group">
                    <input type="hidden" name="id_kategori" value="<?= $data_edit['id_kategori']; ?>">
                    <input type="text" name="nama_kategori" value="<?= $data_edit['nama_kategori']; ?>" required autofocus>
                    <button type="submit" name="edit_category" class="btn-submit">Update</button>
                    <a href="kategori.php" style="color: var(--text-dim); text-decoration: none; font-size: 0.9rem; margin-left: 10px;">Batal</a>
                </form>
            <?php else: ?>
                <h4 style="margin-bottom: 15px;">Tambah Kategori Baru</h4>
                <form action="" method="POST" class="form-group">
                    <input type="text" name="nama_kategori" placeholder="Contoh: Programming, Novel, Desain..." required>
                    <button type="submit" name="add_category" class="btn-submit"><i class="fa-solid fa-plus"></i> Simpan</button>
                </form>
            <?php endif; ?>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th width="80px">No.</th>
                        <th>Nama Kategori</th>
                        <th width="120px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td>
                            <b><?= $no++; ?>.</b> 
                            <span class="id-real">#<?= $row['id_kategori']; ?></span>
                        </td>
                        <td><b><?= $row['nama_kategori']; ?></b></td>
                        <td>
                            <a href="?edit=<?= $row['id_kategori']; ?>" class="btn-edit" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                            <a href="?delete=<?= $row['id_kategori']; ?>" class="btn-delete" onclick="return confirm('Hapus kategori ini?')" title="Hapus"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    
                    <?php if(mysqli_num_rows($result) == 0): ?>
                    <tr>
                        <td colspan="3" style="text-align: center; color: var(--text-dim); padding: 30px;">Belum ada data kategori.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>