<?php

$currentPage = basename($_SERVER['PHP_SELF']);
?>

<link rel="stylesheet" href="../assets/css/include/sidebar_admin.css">
<aside class="sidebar">
    <div class="brand">
        <h2>ADMIN<span>PANEL</span></h2>
        <p class="name">Arten Book</p>
    </div>
    <nav>
        <a href="admin_dashboard.php" class="nav-link <?= ($currentPage == 'admin_dashboard.php') ? 'active' : '' ?>">
            <i class="fa-solid fa-chart-line"></i> Dashboard
        </a>
        <a href="kategori.php" class="nav-link <?= ($currentPage == 'kategori.php') ? 'active' : '' ?>">
            <i class="fa-solid fa-layer-group"></i> Kategori Buku
        </a>
        <a href="buku.php" class="nav-link <?= ($currentPage == 'buku.php') ? 'active' : '' ?>">
            <i class="fa-solid fa-book"></i> Data Buku
        </a>
        <a href="member.php" class="nav-link <?= ($currentPage == 'member.php') ? 'active' : '' ?>">
            <i class="fa-solid fa-users"></i> Data Member
        </a>
        <a href="pesanan.php" class="nav-link <?= ($currentPage == 'pesanan.php') ? 'active' : '' ?>">
            <i class="fa-solid fa-cart-shopping"></i> Pesanan
        </a>
        <a href="../logout.php" class="nav-link logout-link" onclick="return confirm('Yakin ingin keluar?')">
            <i class="fa-solid fa-right-from-bracket"></i> Keluar
        </a>
    </nav>
</aside>