<?php
session_start();
include '../config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['add_to_cart']) && isset($_POST['id_buku'])) {

    $id_buku = mysqli_real_escape_string($conn, $_POST['id_buku']);
    $quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;

    $check_cart = mysqli_query($conn, "SELECT * FROM cart 
        WHERE user_id = '$user_id' AND id_buku = '$id_buku'");

    if (mysqli_num_rows($check_cart) > 0) {

        $cart_item = mysqli_fetch_assoc($check_cart);
        $new_qty = $cart_item['qty'] + $quantity;

        mysqli_query($conn, "UPDATE cart 
            SET qty = '$new_qty' 
            WHERE id_cart = '{$cart_item['id_cart']}' 
            AND user_id = '$user_id'");

    } else {

        mysqli_query($conn, "INSERT INTO cart (user_id, id_buku, qty) 
            VALUES ('$user_id', '$id_buku', '$quantity')");
    }

    header("Location: cart.php");
    exit;
}

if (isset($_POST['increase'])) {

    $id_cart = mysqli_real_escape_string($conn, $_POST['id_cart']);

    $query = mysqli_query($conn, "SELECT cart.qty, buku.stok 
        FROM cart 
        JOIN buku ON cart.id_buku = buku.id_buku 
        WHERE cart.id_cart = '$id_cart' 
        AND cart.user_id = '$user_id'");

    $data = mysqli_fetch_assoc($query);

    if ($data) {
        if ($data['qty'] < $data['stok']) {

            $new_qty = $data['qty'] + 1;

            mysqli_query($conn, "UPDATE cart 
                SET qty = '$new_qty' 
                WHERE id_cart = '$id_cart' 
                AND user_id = '$user_id'");
        }
    }

    header("Location: cart.php");
    exit;
}

if (isset($_POST['decrease'])) {

    $id_cart = mysqli_real_escape_string($conn, $_POST['id_cart']);

    $query = mysqli_query($conn, "SELECT qty 
        FROM cart 
        WHERE id_cart = '$id_cart' 
        AND user_id = '$user_id'");

    $data = mysqli_fetch_assoc($query);

    if ($data && $data['qty'] > 1) {

        $new_qty = $data['qty'] - 1;

        mysqli_query($conn, "UPDATE cart 
            SET qty = '$new_qty' 
            WHERE id_cart = '$id_cart' 
            AND user_id = '$user_id'");
    }

    header("Location: cart.php");
    exit;
}

if (isset($_GET['delete'])) {

    $id_cart = mysqli_real_escape_string($conn, $_GET['delete']);

    mysqli_query($conn, "DELETE FROM cart 
        WHERE id_cart = '$id_cart' 
        AND user_id = '$user_id'");

    header("Location: cart.php");
    exit;
}

$query_cart = mysqli_query($conn, "SELECT cart.*, buku.judul, buku.harga, buku.gambar, buku.stok 
    FROM cart 
    LEFT JOIN buku ON cart.id_buku = buku.id_buku 
    WHERE cart.user_id = '$user_id'");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja | Arten Book</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/user/cart.css">
</head>

<body>


    <?php include '../include/navbar.php'; ?>

    <div class="breadcrumb">
        <a href="../index.php">Beranda</a> / <span>Keranjang Belanja</span>
    </div>

    <main class="main-content">
        <div class="header-title">
            <h1>Keranjang <span>Belanja</span></h1>
            <p>Ada item menarik yang siap kamu miliki hari ini.</p>
        </div>

        <?php if (mysqli_num_rows($query_cart) > 0): ?>
            <div class="cart-grid">
                <div class="cart-list">
                    <?php
                    $subtotal = 0;
                    while ($item = mysqli_fetch_assoc($query_cart)):
                        if (!$item['judul'])
                            continue;
                        $price_item = $item['harga'] * $item['qty'];
                        $subtotal += $price_item;
                        ?>
                        <div class="item-row">
                            <div class="book-detail">
                                <?php
                                $gambar_path = "../assets/image/books/" . htmlspecialchars($item['gambar']);
                                if (!empty($item['gambar']) && file_exists($gambar_path)): ?>
                                    <img src="<?= $gambar_path ?>" alt="Cover">
                                <?php else: ?>
                                    <img src="../assets/image/books/default.jpg" alt="Cover"
                                        style="background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                                <?php endif; ?>
                                <div class="book-info">
                                    <h4><?= htmlspecialchars($item['judul']); ?></h4>
                                    <p>Rp <?= number_format($item['harga'], 0, ',', '.'); ?></p>
                                    <p style="color: #27ae60; font-size: 0.7rem;">Stok: <?= $item['stok']; ?></p>
                                </div>
                            </div>

                            <div class="qty-control">
                                <form action="" method="POST" style="display: inline;">
                                    <input type="hidden" name="id_cart" value="<?= $item['id_cart']; ?>">
                                    <button type="submit" name="decrease" class="qty-btn" <?= ($item['qty'] <= 1) ? 'disabled' : ''; ?>>-</button>
                                </form>

                                <span class="qty-value"><?= $item['qty']; ?></span>

                                <form action="" method="POST" style="display: inline;">
                                    <input type="hidden" name="id_cart" value="<?= $item['id_cart']; ?>">
                                    <button type="submit" name="increase" class="qty-btn" <?= ($item['qty'] >= $item['stok']) ? 'disabled' : ''; ?>>+</button>
                                </form>
                            </div>

                            <div class="price-sub">Rp <?= number_format($price_item, 0, ',', '.'); ?></div>
                            <a href="?delete=<?= $item['id_cart']; ?>" onclick="return confirm('Hapus item ini?')"
                                class="delete-btn"><i class="fa-solid fa-trash-can"></i></a>
                        </div>
                    <?php endwhile;

                    $biaya_admin = 2000;
                    $ongkir = 0;
                    $total_akhir = $subtotal + $biaya_admin + $ongkir;
                    ?>
                </div>

                <div class="summary-card">
                    <h3>Ringkasan Belanja</h3>
                    <div class="summary-item">
                        <span>Subtotal Produk</span>
                        <span>Rp <?= number_format($subtotal, 0, ',', '.'); ?></span>
                    </div>
                    <div class="summary-item">
                        <span>Biaya Admin</span>
                        <span>Rp <?= number_format($biaya_admin, 0, ',', '.'); ?></span>
                    </div>
                    <div class="summary-item">
                        <span>Biaya Pengiriman</span>
                        <span style="color: #27ae60; font-weight: 700;">Gratis</span>
                    </div>

                    <div class="summary-total">
                        <span style="font-weight: 700;">Total Harga</span>
                        <span>Rp <?= number_format($total_akhir, 0, ',', '.'); ?></span>
                    </div>

                    <a href="checkout.php" class="btn-checkout">
                        <i class="fas fa-arrow-right"></i> Checkout Sekarang
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fa-solid fa-basket-shopping"></i>
                </div>

                <h2>Keranjang Kamu Masih Kosong</h2>
                <p>Sepertinya kamu belum menambahkan buku apapun. Yuk mulai cari buku favoritmu sekarang!</p>

                <a href="../index.php" class="btn-explore">
                    <i class="fas fa-book-open"></i> Jelajahi Buku
                </a>
            </div>
        <?php endif; ?>
    </main>

    <?php include '../include/footer.php'; ?>
</body>

</html>