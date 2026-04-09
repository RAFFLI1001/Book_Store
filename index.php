<?php
session_start();
include 'config.php';

$search = $_GET['search'] ?? '';
$kategori = $_GET['kategori'] ?? '';

$where = [];

if (!empty($search)) {
    $search_safe = mysqli_real_escape_string($conn, $search);
    $where[] = "(b.judul LIKE '%$search_safe%' OR b.penulis LIKE '%$search_safe%')";
}

if (!empty($kategori)) {
    $kategori_safe = mysqli_real_escape_string($conn, $kategori);
    $where[] = "b.id_kategori = '$kategori_safe'";
}

$query = "
SELECT b.*, k.nama_kategori 
FROM buku b
LEFT JOIN kategori k ON b.id_kategori = k.id_kategori
";

if (!empty($where)) {
    $query .= " WHERE " . implode(" AND ", $where);
}

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arten Book - Toko Buku Online</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/index.css">
</head>

<body>
    <?php include 'include/navbar.php'; ?>

    <form class="search-bar" method="GET" action="<?= $navbar_path ?>index.php">

        <input type="text" name="search" placeholder="Cari buku, penulis..."
            value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">

        <select name="kategori">
            <option value="">Semua</option>
            <?php
            $kategori_list = mysqli_query($conn, "SELECT * FROM kategori");
            while ($k = mysqli_fetch_assoc($kategori_list)): ?>
                <option value="<?= $k['id_kategori']; ?>" <?= ($_GET['kategori'] ?? '') == $k['id_kategori'] ? 'selected' : ''; ?>>
                    <?= $k['nama_kategori']; ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button type="submit">
            <i class="fas fa-search"></i>
        </button>

    </form>





    <div class="section">
        <div class="section-header">
            <h3>Buku Tersedia</h3>
        </div>

        <div class="book-grid">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php
                $count = 0;
                $image_folder = "assets/image/books/";

                while ($row = mysqli_fetch_assoc($result)):
                    $count++;
                    $price = $row['harga'];

                    $image_path = null;
                    if (!empty($row['gambar']) && file_exists($image_folder . $row['gambar'])) {
                        $image_path = $image_folder . $row['gambar'];
                    } else {
                        $judul_clean = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($row['judul']));
                        $image_files = glob($image_folder . "*" . $judul_clean . "*.{jpg,jpeg,png,gif,jfif}", GLOB_BRACE);

                        if (empty($image_files)) {
                            $keywords = explode(' ', strtolower($row['judul']));
                            foreach ($keywords as $keyword) {
                                if (strlen($keyword) > 3) {
                                    $image_files = glob($image_folder . "*" . $keyword . "*.{jpg,jpeg,png,gif,jfif}", GLOB_BRACE);
                                    if (!empty($image_files))
                                        break;
                                }
                            }
                        }
                        $image_path = !empty($image_files) ? $image_files[0] : null;
                    }
                    ?>
                    <div class="book-card">
                        <div class="book-image-container">
                            <div class="book-image">
                                <?php if ($image_path && file_exists($image_path)): ?>
                                    <img src="<?= $image_path ?>" alt="<?= htmlspecialchars($row['judul']) ?>">
                                <?php else: ?>
                                    <div class="book-image-no-img">
                                        <i class="fas fa-book-open"></i>
                                        <span><?= htmlspecialchars(substr($row['judul'], 0, 20)) ?>...</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="book-info">
                            <div class="book-title"><?= htmlspecialchars($row['judul']) ?></div>
                            <div class="book-author">
                                <i class="fas fa-user-edit"></i> <?= htmlspecialchars($row['penulis']) ?>
                            </div>
                            <div class="book-price">
                                <span class="price-current">
                                    Rp <?= number_format($row['harga'], 0, ',', '.') ?>
                                </span>
                            </div>

                            <div class="book-actions">
                                <a href="detail_buku.php?id=<?= $row['id_buku'] ?>" class="btn-detail">
                                    <i class="fas fa-book-open"></i> Lihat Buku
                                </a>

                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="grid-column: 1/-1; text-align:center; padding:50px;">Belum ada buku tersedia.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'include/footer.php'; ?>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php if (!empty($search)): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                <?php if (mysqli_num_rows($result) > 0): ?>
                    Swal.fire({
                        icon: 'success',
                        title: 'Hasil ditemukan!',
                        text: 'Ditemukan <?= mysqli_num_rows($result) ?> buku dari pencarian "<?= htmlspecialchars($search) ?>"',
                        timer: 2000,
                        showConfirmButton: false
                    });
                <?php else: ?>
                    Swal.fire({
                        icon: 'error',
                        title: 'Tidak ditemukan!',
                        text: 'Buku dengan kata kunci "<?= htmlspecialchars($search) ?>" tidak ada 😢',
                    });
                <?php endif; ?>
            });
        </script>
    <?php endif; ?>
</body>


</html>