<?php
session_start();
include '../config.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - Arten Book</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/pages/about.css">
</head>

<body>
    <?php include '../include/navbar.php'; ?>

    <section class="about-hero">
        <h1><i class="fas fa-book-open"></i> Arten Book</h1>
        <p>Kami hadir untuk mendekatkan buku kepada setiap pembaca. Temukan ribuan judul pilihan dengan harga terbaik,
            diantarkan langsung ke tangan Anda.</p>
    </section>

    <section class="about-section" >
        <h2>Siapa Kami?</h2>
        <p>
            <strong>Arten Book</strong> adalah toko buku online yang didirikan dengan semangat untuk memperluas akses
            literasi di Indonesia. Kami percaya bahwa setiap orang berhak mendapatkan bacaan berkualitas tanpa hambatan
            jarak maupun harga.
        </p>
        <p>
            Mulai dari buku pendidikan, fiksi, pengembangan diri, hingga komik dan majalah, Arten Book menyediakan
            pilihan lengkap yang dikurasi dengan cermat untuk memenuhi kebutuhan berbagai kalangan pembaca.
        </p>
    </section>

    <hr class="section-divider">

    <section class="about-section">
        <h2>Visi & Misi</h2>
        <div class="visi-misi-grid">
            <div class="visi-misi-card">
                <h3><i class="fas fa-eye"></i> Visi</h3>
                <p>Menjadi platform toko buku online terpercaya dan terlengkap di Indonesia yang mendorong budaya
                    membaca di seluruh lapisan masyarakat.</p>
            </div>
            <div class="visi-misi-card">
                <h3><i class="fas fa-bullseye"></i> Misi</h3>
                <ul>
                    <li>Menyediakan koleksi buku berkualitas dengan harga terjangkau</li>
                    <li>Memberikan pengalaman belanja buku yang mudah dan menyenangkan</li>
                    <li>Mendukung penulis dan penerbit lokal Indonesia</li>
                    <li>Mengantar buku ke seluruh penjuru Indonesia dengan cepat dan aman</li>
                </ul>
            </div>
        </div>
    </section>

    <section class="about-section">
        <h2>Nilai-Nilai Kami</h2>
        <div class="nilai-grid">
            <div class="nilai-card">
                <i class="fas fa-shield-alt"></i>
                <h4>Kepercayaan</h4>
                <p>Kami berkomitmen memberikan produk asli dan layanan yang bisa Anda andalkan setiap saat.</p>
            </div>
            <div class="nilai-card">
                <i class="fas fa-heart"></i>
                <h4>Passion Literasi</h4>
                <p>Kecintaan kami pada buku menjadi fondasi dalam setiap keputusan bisnis yang kami ambil.</p>
            </div>
            <div class="nilai-card">
                <i class="fas fa-star"></i>
                <h4>Kualitas</h4>
                <p>Setiap buku yang kami jual telah melalui seleksi ketat untuk memastikan kualitas terbaik.</p>
            </div>
        </div>
    </section>

    <hr class="section-divider">
    <section style="background: #f0f7ff; padding: 60px 20px; text-align: center;">
        <h2 style="font-size: 1.8rem; font-weight: 700; color: #000000; margin-bottom: 12px;">Mulai Perjalanan Membaca
            Anda</h2>
        <p style="color: #555; margin-bottom: 28px;">Temukan buku favorit Anda dari koleksi kami yang terus bertambah
            setiap harinya.</p>
        <a href="index.php"
            style="background: #3498db; color: #fff; padding: 14px 36px; border-radius: 50px; font-weight: 600; text-decoration: none; font-size: 1rem; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-book-open"></i> Jelajahi Koleksi Buku
        </a>
    </section>

    <?php include '../include/footer.php'; ?>
</body>

</html>