<?php
session_start();
include 'config.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id_buku = mysqli_real_escape_string($conn, $_GET['id']);

$query = "SELECT * FROM buku WHERE id_buku = '$id_buku'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: index.php");
    exit;
}

$buku = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $buku_id = $_POST['id_buku'];
    $quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;

    $check_cart = mysqli_query($conn, "SELECT * FROM cart WHERE user_id = '$user_id' AND id_buku = '$buku_id'");

    if (mysqli_num_rows($check_cart) > 0) {

        $cart_item = mysqli_fetch_assoc($check_cart);
        $new_qty = $cart_item['qty'] + $quantity;
        mysqli_query($conn, "UPDATE cart SET qty = '$new_qty' WHERE id_cart = '{$cart_item['id_cart']}'");
    } else {

        mysqli_query($conn, "INSERT INTO cart (user_id, id_buku, qty) VALUES ('$user_id', '$buku_id', '$quantity')");
    }

    if (isset($_POST['redirect_to_checkout'])) {
    header("Location: user/checkout.php");
} else {
    header("Location: user/cart.php");
}
exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($buku['judul']) ?> - Arten Book</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/detail_buku.css">

</head>

<body>

    <?php include 'include/navbar.php'; ?>
    <div class="breadcrumb">
        <a href="index.php">Beranda</a> / <span><?= htmlspecialchars($buku['judul']) ?></span>
    </div>


    <div class="detail-container">
        <div class="detail-card">
            <div class="book-cover-section">
                <div class="book-cover-large">
                    <?php
                    $image_folder = "assets/image/books/";
                    $image_path = null;

                    if (!empty($buku['gambar']) && file_exists($image_folder . $buku['gambar'])) {
                        $image_path = $image_folder . $buku['gambar'];
                    }

                    if ($image_path && file_exists($image_path)): ?>
                        <img src="<?= $image_path ?>" alt="<?= htmlspecialchars($buku['judul']) ?>">
                    <?php else: ?>
                        <div class="no-image">
                            <i class="fas fa-book-open"></i>
                            <p style="margin-top: 15px;"><?= htmlspecialchars($buku['judul']) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="book-info-section">
                <div class="book-category">
                    <i class="fas fa-tag"></i> Buku
                </div>

                <h1 class="book-title-large"><?= htmlspecialchars($buku['judul']) ?></h1>

                <div class="book-author-large">
                    <i class="fas fa-user-edit"></i> <?= htmlspecialchars($buku['penulis']) ?>
                </div>



                <div class="book-price-large">
                    <span class="price-large">Rp <?= number_format($buku['harga'], 0, ',', '.') ?></span>
                </div>

                <div class="book-stock">
                    <?php if ($buku['stok'] > 0): ?>
                        <span class="stock-available"><i class="fas fa-check-circle"></i> Stok Tersedia
                            (<?= $buku['stok'] ?> pcs)</span>
                    <?php else: ?>
                        <span class="stock-out"><i class="fas fa-times-circle"></i> Stok Habis</span>
                    <?php endif; ?>
                </div>

                <div class="book-description">
                    <h4>Deskripsi Buku</h4>
                    <p><?= !empty($buku['deskripsi'])
                        ? nl2br(htmlspecialchars($buku['deskripsi']))
                        : '<i style="color:#999;">Deskripsi belum tersedia.</i>' ?></p>
                </div>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($buku['stok'] > 0): ?>
                        <form method="POST" action="">
                            <input type="hidden" name="id_buku" value="<?= $buku['id_buku'] ?>">

                            <div class="quantity-section">
                                <span class="quantity-label">Jumlah:</span>
                                <div class="quantity-control">
                                    <button type="button" class="quantity-btn" onclick="decrementQuantity()">-</button>
                                    <input type="number" name="quantity" id="quantity" class="quantity-input" value="1" min="1"
                                        max="<?= $buku['stok'] ?>">
                                    <button type="button" class="quantity-btn" onclick="incrementQuantity()">+</button>
                                </div>
                            </div>

                            <div class="detail-actions">
                                <button type="submit" name="add_to_cart" class="btn-add-cart">
                                    <i class="fas fa-shopping-cart"></i> Tambahkan ke Keranjang
                                </button>
                                <button type="button" class="btn-buy-now" onclick="buyNow()">
                                    <i class="fas fa-bolt"></i> Beli Sekarang
                                </button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="login-warning">
                            <i class="fas fa-exclamation-triangle"></i> Maaf, stok buku ini sedang habis.
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="login-warning">
                        <i class="fas fa-lock"></i> Silakan <a href="login.php">login</a> terlebih dahulu untuk membeli buku
                        ini.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include 'include/footer.php'; ?>

    <script>
        function incrementQuantity() {
            let qtyInput = document.getElementById('quantity');
            let currentValue = parseInt(qtyInput.value);
            let maxStock = <?= $buku['stok'] ?>;

            if (currentValue < maxStock) {
                qtyInput.value = currentValue + 1;
            }
        }

        function decrementQuantity() {
            let qtyInput = document.getElementById('quantity');
            let currentValue = parseInt(qtyInput.value);

            if (currentValue > 1) {
                qtyInput.value = currentValue - 1;
            }
        }

        function buyNow() {
            let form = document.createElement('form');
            form.method = 'POST';
            form.action = '';

            let idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'id_buku';
            idInput.value = '<?= $buku['id_buku'] ?>';

            let qtyInput = document.createElement('input');
            qtyInput.type = 'hidden';
            qtyInput.name = 'quantity';
            qtyInput.value = document.getElementById('quantity').value;

            let actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'add_to_cart';
            actionInput.value = '1';

            let redirectInput = document.createElement('input');
            redirectInput.type = 'hidden';
            redirectInput.name = 'redirect_to_checkout';
            redirectInput.value = '1';

            form.appendChild(idInput);
            form.appendChild(qtyInput);
            form.appendChild(actionInput);
            form.appendChild(redirectInput);
            document.body.appendChild(form);
            form.submit();
        }
    </script>

</body>

</html>