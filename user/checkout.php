<?php
session_start();
include '../config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') { 
    header("Location: ../login.php"); 
    exit; 
}

$user_id = $_SESSION['user_id'];

$query_user = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$data_user = mysqli_fetch_assoc($query_user);

$query_cart = mysqli_query($conn, "SELECT cart.*, buku.judul, buku.harga, buku.gambar, buku.stok
                                   FROM cart 
                                   JOIN buku ON cart.id_buku = buku.id_buku 
                                   WHERE cart.user_id = '$user_id'");

if (mysqli_num_rows($query_cart) == 0) {
    header("Location: cart.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | Arten Book</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/user/checkout.css">
</head>
<body>

<?php include '../include/navbar.php'; ?>
<div class="breadcrumb">
    <a href="../index.php">Beranda</a> / 
    <a href="cart.php">Keranjang</a> / 
    <span>Checkout</span>
</div>

<main class="main-content">
    <div class="header-title">
        <h1>Checkout</h1>
        <p>Lengkapi data diri dan pilih metode pembayaran</p>
    </div>

    <form action="proses_pesanan.php" method="POST">
        <div class="checkout-grid">
            <div class="left-col">
                <div class="form-card">
                    <h3><i class="fas fa-map-marker-alt"></i> Alamat Pengiriman</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Nama Penerima</label>
                            <input type="text" name="nama" class="form-input" value="<?= htmlspecialchars($data_user['fullname'] ?? $data_user['nama'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fab fa-whatsapp"></i> No. WhatsApp</label>
                            <input type="text" name="phone" class="form-input" value="<?= htmlspecialchars($data_user['phone'] ?? '') ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-location-dot"></i> Alamat Lengkap</label>
                        <textarea name="alamat" class="form-input" rows="3" placeholder="Jl. Contoh No. 123, Kec. Contoh, Kota..." required></textarea>
                    </div>
                </div>

                <div class="form-card">
                    <h3><i class="fas fa-shopping-bag"></i> Review Produk</h3>
                    <div class="item-list">
                        <?php 
                        $subtotal = 0;
                        while($item = mysqli_fetch_assoc($query_cart)): 
                            $total_per_item = $item['harga'] * $item['qty'];
                            $subtotal += $total_per_item;
                        ?>
                        <div class="item-row">
                            <?php 
                            $gambar_path = "../assets/image/books/" . htmlspecialchars($item['gambar']);
                            if (!empty($item['gambar']) && file_exists($gambar_path)): ?>
                                <img src="<?= $gambar_path ?>" alt="Cover">
                            <?php else: ?>
                                <img src="../assets/image/books/default.jpg" alt="Cover">
                            <?php endif; ?>
                            <div class="item-info">
                                <h4><?= htmlspecialchars($item['judul']); ?></h4>
                                <p><?= $item['qty']; ?> Buku x Rp <?= number_format($item['harga'], 0, ',', '.'); ?></p>
                            </div>
                            <div class="item-price">Rp <?= number_format($total_per_item, 0, ',', '.'); ?></div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
            <div class="right-col">

                <div class="summary-card">
                    <h3>Ringkasan Pembayaran</h3>
                    <div class="calc-row">
                        <span>Subtotal Produk</span>
                        <span>Rp <?= number_format($subtotal, 0, ',', '.'); ?></span>
                    </div>
                    <div class="calc-row">
                        <span>Biaya Admin</span>
                        <span>Rp 2.000</span>
                    </div>
                    <div class="calc-row">
                        <span>Ongkos Kirim</span>
                        <span><span class="badge-free">GRATIS</span></span>
                    </div>
                    <div class="calc-row total">
                        <span>Total Bayar</span>
                        <span>Rp <?= number_format($subtotal + 2000, 0, ',', '.'); ?></span>
                    </div>
                    <input type="hidden" name="total_bayar" value="<?= $subtotal + 2000; ?>">
                </div>

 
                <div class="payment-card">
                    <h3><i class="fas fa-credit-card"></i> Metode Pembayaran</h3>
                    <div class="payment-grid">
                        <div class="payment-item">
                            <input type="radio" name="metode_bayar" id="bank" value="Transfer Bank" onclick="pilihBayar('bank')" required>
                            <label for="bank" class="payment-label"><i class="fas fa-university"></i><span>Bank</span></label>
                        </div>
                        <div class="payment-item">
                            <input type="radio" name="metode_bayar" id="qris" value="QRIS" onclick="pilihBayar('qris')">
                            <label for="qris" class="payment-label"><i class="fas fa-qrcode"></i><span>QRIS</span></label>
                        </div>
                        <div class="payment-item">
                            <input type="radio" name="metode_bayar" id="cod" value="COD" onclick="pilihBayar('cod')">
                            <label for="cod" class="payment-label"><i class="fas fa-hand-holding-usd"></i><span>COD</span></label>
                        </div>
                    </div>

                    <div id="payment-box" class="payment-instruction">
                        <div id="content-bank" class="instruction-content" style="display:none;">
                            <p style="font-size: 0.8rem;">Transfer ke rekening berikut:</p>
                            <div class="bank-info">
                                <span>Bank BCA</span>
                                <strong>123 4567 890</strong>
                                <span>A/N: Arten Bookstore</span>
                            </div>
                            <div class="bank-info">
                                <span>Bank Mandiri</span>
                                <strong>987 6543 210</strong>
                                <span>A/N: Arten Bookstore</span>
                            </div>
                        </div>

                        <div id="content-qris" class="instruction-content" style="display:none;">
                            <p style="font-size: 0.8rem;">Scan QRIS di bawah ini untuk pembayaran:</p>
                            <img src="../assets/image/qris.jpeg" class="qris-image" alt="QRIS">
                            <p style="font-size: 0.7rem; color: #999; margin-top: 10px;">Atau klik tombol konfirmasi setelah melakukan pembayaran</p>
                        </div>

                        <div id="content-cod" class="instruction-content" style="display:none;">
                            <p style="font-size: 0.9rem; font-weight: 600;">Cash On Delivery</p>
                            <p style="font-size: 0.75rem; color: #999;">Siapkan uang tunai saat kurir datang.</p>
                        </div>
                    </div>

                    <button type="submit" class="btn-confirm">
                        <i class="fas fa-check-circle"></i> Konfirmasi & Bayar
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </form>
</main>

<?php include '../include/footer.php'; ?>

<script>
    function pilihBayar(tipe) {
        const box = document.getElementById('payment-box');
        box.style.display = 'block';

        document.getElementById('content-bank').style.display = 'none';
        document.getElementById('content-qris').style.display = 'none';
        document.getElementById('content-cod').style.display = 'none';

        if(tipe === 'bank') {
            document.getElementById('content-bank').style.display = 'block';
        } else if(tipe === 'qris') {
            document.getElementById('content-qris').style.display = 'block';
        } else if(tipe === 'cod') {
            document.getElementById('content-cod').style.display = 'block';
        }
    }
</script>

</body>
</html>