<?php
session_start();
include '../config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { 
    header("Location: ../login.php"); 
    exit; 
}

$search = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $query_sql = "SELECT * FROM users 
                  WHERE role = 'user' 
                  AND (username LIKE '%$search%' 
                  OR email LIKE '%$search%'
                  OR phone LIKE '%$search%')
                  ORDER BY id DESC";
} else {
    $query_sql = "SELECT * FROM users WHERE role = 'user' ORDER BY id DESC";
}

$query_member = mysqli_query($conn, $query_sql);
$total_member = ($query_member) ? mysqli_num_rows($query_member) : 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Member | M&N Edition</title>
   <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin/member.css">
</head>
<body>

    <?php include '../include/sidebar_admin.php'; ?>
    <main class="main-content">
        <div class="header-flex">
            <h1>Data <span>Member</span></h1>
            <div class="stats-badge">Total: <b><?= $total_member; ?></b> Member</div>
        </div>

        <form action="" method="GET" class="search-form">
            <div class="search-wrapper">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="search" class="search-input" placeholder="Cari username, email, atau telepon..." value="<?= htmlspecialchars($search); ?>">
            </div>
            <button type="submit" class="btn-search">Cari</button>
        </form>
        
        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Gabung</th>
                        <th>Role</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($total_member > 0): ?>
                        <?php 
                        $no = 1;
                        while($m = mysqli_fetch_assoc($query_member)): 
                        ?>
                        <tr>
                            <td class="no-column">#<?= $no++; ?></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div class="user-avatar"><?= substr($m['username'], 0, 1); ?></div>
                                    <b><?= htmlspecialchars($m['username']); ?></b>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($m['email']); ?></td>
                            <td class="phone-text"><?= $m['phone'] ? htmlspecialchars($m['phone']) : '-'; ?></td>
                            <td><?= date('d/m/y', strtotime($m['created_at'])); ?></td>
                            <td><span class="role-badge"><?= strtoupper($m['role']); ?></span></td>
                          
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align: center; padding: 50px; color: var(--text-dim);">Data tidak ditemukan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>