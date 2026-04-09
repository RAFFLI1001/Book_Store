<?php
session_start();
include '../config.php';


if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

if (isset($_POST['update_status'])) {
    $id_order = $_POST['id_order'];
    $status_baru = $_POST['status'];

    mysqli_query($conn, "UPDATE orders SET status = '$status_baru' WHERE id = '$id_order'");

    echo "<script>
        alert('Status berhasil diubah!');
        window.location.href='pesanan.php';
    </script>";
    exit;
}


if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM orders WHERE id = '$id'");
    header("Location: pesanan.php");
    exit;
}

$query_orders = "SELECT * FROM orders ORDER BY order_date DESC";
$result = mysqli_query($conn, $query_orders);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Daftar Pesanan | Admin M&N</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin/pesanan.css">
</head>

<body>
    <?php include '../include/sidebar_admin.php'; ?>

    <main class="main-content">
        <h1>Daftar <span>Pesanan</span></h1>
        <p class="subtitle">Kelola transaksi masuk dari member M&N Edition</p>

        <div class="table-card">
            <?php

            $query_orders = "
SELECT o.*, b.judul AS nama_buku, b.gambar AS cover
FROM orders o
LEFT JOIN buku b ON o.book_id = b.id_buku
ORDER BY o.order_date DESC
";
            $result = mysqli_query($conn, $query_orders);
            ?>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Member</th>
                        <th>Buku</th>
                        <th>No HP</th>
                        <th>Alamat</th>
                        <th>Metode</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td>#ORD-<?= $row['id']; ?></td>
                            <td><b><?= $row['nama']; ?></b></td>
                            <td>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <?php if (!empty($row['cover'])): ?>
                                        <img src="../assets/image/books/<?= htmlspecialchars($row['cover']); ?>"
                                            alt="<?= htmlspecialchars($row['nama_buku']); ?>"
                                            style="width:45px; height:63px; object-fit:cover; border-radius:5px; flex-shrink:0;">
                                    <?php else: ?>
                                        <div style="width:45px; height:63px; background:#1e293b; border-radius:5px;
                        display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                            <i class="fa fa-book" style="color:#818cf8"></i>
                                        </div>
                                    <?php endif; ?>
                                    <span style="font-size:0.85rem; line-height:1.4; color:#f8fafc; font-weight:500;">
                                        <?= !empty($row['nama_buku']) ? htmlspecialchars($row['nama_buku']) : '<i style="color:#ef4444">-</i>'; ?>
                                    </span>
                                </div>
                            </td>
                            <td><?= $row['phone']; ?></td>
                            <td><?= $row['alamat']; ?></td>
                            <td><?= $row['metode_bayar']; ?></td>
                            <td><b style="color:var(--accent-purple)">
                                    Rp <?= number_format($row['total_amount'], 0, ',', '.'); ?>
                                </b></td>
                            <td>
                                <span class="badge <?= $row['status']; ?>">
                                    <?= $row['status']; ?>
                                </span>
                            </td>
                            <td>
                                <form action="" method="POST"
                                    style="display:flex; flex-wrap:wrap; gap:6px; align-items:center;">
                                    <input type="hidden" name="id_order" value="<?= $row['id']; ?>">
                                    <select name="status" style="flex:1; min-width:95px; max-width:110px;">
                                        <option value="pending" <?= $row['status'] == 'pending' ? 'selected' : ''; ?>>Pending
                                        </option>
                                        <option value="paid" <?= $row['status'] == 'paid' ? 'selected' : ''; ?>>Paid</option>
                                        <option value="shipped" <?= $row['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped
                                        </option>
                                        <option value="delivered" <?= $row['status'] == 'delivered' ? 'selected' : ''; ?>>
                                            Delivered</option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn-update" title="Simpan">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                    <a href="?delete=<?= $row['id']; ?>" onclick="return confirm('Hapus data pesanan ini?')"
                                        class="btn-delete" title="Hapus">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>

</html>