<?php
session_start();
include '../config.php';

// Proteksi Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { 
    header("Location: ../login.php"); 
    exit; 
}

// --- AMBIL DATA STATISTIK ---
// Total Buku
$q_buku = mysqli_query($conn, "SELECT COUNT(*) as total FROM buku");
$total_buku = mysqli_fetch_assoc($q_buku)['total'];

// Total Member
$q_member = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'user'");
$total_member = mysqli_fetch_assoc($q_member)['total'];

// Total Pesanan (Gunakan tabel 'orders' sesuai database)
$q_orders = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders");
$total_orders = mysqli_fetch_assoc($q_orders)['total'];

// --- AMBIL PESANAN TERBARU ---
$query_recent = mysqli_query($conn, "SELECT orders.*, users.username 
    FROM orders 
    JOIN users ON orders.user_id = users.id 
    ORDER BY orders.id DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | M&N Edition</title>
   <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin/admin_dashboard.css">
</head>
<body>

    <?php include '../include/sidebar_admin.php'; ?>

    <main class="main-content">
        <h1>Dashboard <span>Overview</span></h1>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Koleksi Buku</h3>
                <div class="value"><?= $total_buku; ?> <span>Buku</span></div>
            </div>
            <div class="stat-card">
                <h3>Member Terdaftar</h3>
                <div class="value"><?= $total_member; ?> <span>User</span></div>
            </div>
            <div class="stat-card">
                <h3>Pesanan Masuk</h3>
                <div class="value"><?= $total_orders; ?> <span>Order</span></div>
            </div>
        </div>

        <div class="table-card">
            <h2>Pesanan Terbaru</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID Order</th>
                        <th>Username</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($query_recent) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($query_recent)): ?>
                        <tr>
                            <td style="color: var(--text-dim);">#<?= $row['id']; ?></td>
                            <td style="font-weight: 700;"><?= htmlspecialchars($row['username']); ?></td>
                            <td><?= date('d M Y', strtotime($row['order_date'])); ?></td>
                            <td style="color: var(--accent-purple); font-weight: 700;">Rp <?= number_format($row['total_amount'], 0, ',', '.'); ?></td>
                            <td><span class="status-badge"><?= $row['status']; ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align: center; color: var(--text-dim); padding: 40px;">Belum ada pesanan terbaru.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>