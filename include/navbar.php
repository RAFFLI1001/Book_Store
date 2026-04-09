<?php
if (!isset($navbar_path)) {
    $calling_dir = dirname($_SERVER['SCRIPT_FILENAME']);
    $root_dir = dirname(__FILE__);

    $navbar_path = rtrim(str_repeat('../', substr_count(
        str_replace($root_dir, '', $calling_dir),
        DIRECTORY_SEPARATOR
    ) + 1), '/') . '/';

    if (!file_exists($navbar_path . 'config.php')) {
        $navbar_path = './';
        if (!file_exists($navbar_path . 'config.php')) {
            $navbar_path = '../';
        }
    }
}
?>  

<link rel="stylesheet" href="<?= $navbar_path ?>assets/css/include/navbar.css">

<header class="main-header">
    <div class="header-container">

        <a href="<?= $navbar_path ?>index.php" class="logo" style="text-decoration:none;">
            <i class="fas fa-book-open"></i>
            <div>
                <h1>Arten Book</h1>
            </div>
        </a>
        <nav class="main-nav">
            <a href="<?= $navbar_path ?>index.php"
                class="nav-link <?= $current_page === 'index.php' ? 'active' : '' ?>">
                <i class="fas fa-home"></i> Beranda
            </a>
            <a href="<?= $navbar_path ?>../pages/about.php"
                class="nav-link <?= $current_page === 'about.php' ? 'active' : '' ?>">
                <i class="fas fa-info-circle"></i> Tentang
            </a>
            <a href="<?= $navbar_path ?>../pages/contact.php"
                class="nav-link <?= $current_page === 'contact.php' ? 'active' : '' ?>">
                <i class="fas fa-headset"></i> Kontak
            </a>
        </nav>
        <div class="user-actions">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?= $navbar_path ?>user/cart.php">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Keranjang</span>
                </a>
                <a href="<?= $navbar_path ?>user/riwayat_pesanan.php">
                    <i class="fas fa-history"></i>
                    <span>Riwayat</span>
                </a>
                <a href="<?= $navbar_path ?>user/profile.php">
                    <i class="fas fa-user-circle"></i>
                    <span><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></span>
                </a>
                <a href="<?= $navbar_path ?>logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            <?php else: ?>
                <a href="<?= $navbar_path ?>login.php">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Login</span>
                </a>
                <a href="<?= $navbar_path ?>../user/register.php">
                    <i class="fas fa-user-plus"></i>
                    <span>Register</span>
                </a>
            <?php endif; ?>
        </div>

    </div>
</header>