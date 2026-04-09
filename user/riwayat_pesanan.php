<?php
session_start();
include '../config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') { 
    header("Location: ../login.php"); 
    exit; 
}

$user_id = $_SESSION['user_id'];

$query_pesanan = mysqli_query($conn, "SELECT * FROM orders 
WHERE user_id = '$user_id' 
ORDER BY order_date DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan | Arten Book</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/user/riwayat_pesanan.css">
</head>
<body>

<?php include '../include/navbar.php'; ?>

<div class="breadcrumb">
    <a href="../index.php">Beranda</a> / <span>Riwayat Pesanan</span>
</div>

<main class="main-content">
    <div class="header-title">
        <h1>Riwayat <span>Pesanan</span></h1>
        <p>Pantau status pesanan buku impianmu di sini.</p>
    </div>

    <div style="margin-top: 30px;">
        <?php if(mysqli_num_rows($query_pesanan) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($query_pesanan)): 
                $status_class = '';
                switch($row['status']) {
                    case 'pending':
                        $status_class = 'status-pending';
                        $status_text = 'Menunggu Pembayaran';
                        break;
                    case 'proses':
                        $status_class = 'status-proses';
                        $status_text = 'Sedang Diproses';
                        break;
                    case 'kirim':
                        $status_class = 'status-kirim';
                        $status_text = 'Sedang Dikirim';
                        break;
                    case 'selesai':
                        $status_class = 'status-selesai';
                        $status_text = 'Selesai';
                        break;
                    case 'batal':
                        $status_class = 'status-batal';
                        $status_text = 'Dibatalkan';
                        break;
                    default:
                        $status_class = 'status-pending';
                        $status_text = $row['status'];
                }
            ?>
                <div class="order-card">
                    <div class="order-info">
                        <span class="order-id">
                            <i class="fas fa-hashtag"></i> ORDER #<?= str_pad($row['id'], 6, '0', STR_PAD_LEFT); ?>
                        </span>
                        <div class="order-date">
                            <i class="fas fa-calendar-alt" style="color: #e31e24; margin-right: 8px;"></i>
                            <?= date('d F Y, H:i', strtotime($row['order_date'])); ?>
                        </div>
                        <div class="order-address">
                            <i class="fas fa-location-dot"></i>
                            <?= htmlspecialchars($row['alamat']); ?>
                        </div>
                        <div>
                            <span class="price-tag">
                                Rp <?= number_format($row['total_amount'], 0, ',', '.'); ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="right-section">
                        <div class="status-badge <?= $status_class; ?>">
                            <i class="fas <?= ($row['status'] == 'selesai') ? 'fa-check-circle' : (($row['status'] == 'batal') ? 'fa-times-circle' : 'fa-clock') ; ?>"></i>
                            <?= $status_text; ?>
                        </div>
                        <div class="payment-method">
                            <i class="fas fa-credit-card"></i> Metode: <?= htmlspecialchars($row['metode_bayar'] ?? 'Transfer Bank'); ?>
                        </div>
                        <a href="https://wa.me/6285810077475?text=Halo%20Arten%20Book,%20saya%20ingin%20bertanya%20tentang%20pesanan%20saya%20dengan%20ID%20ORDER%20#<?= str_pad($row['id'], 6, '0', STR_PAD_LEFT); ?>" 
                           target="_blank" 
                           class="btn-wa">
                            <i class="fab fa-whatsapp"></i> Chat Admin
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-receipt"></i>
                <h2>Belum Ada Pesanan</h2>
                <p>Yuk, mulai belanja buku favoritmu sekarang!</p>
                <a href="../index.php" class="btn-explore"><i class="fas fa-book-open"></i> Cari Buku</a>
            </div>
        <?php endif; ?>
    </div>
</main>
<?php include '../include/footer.php'; ?>


</body>
</html>