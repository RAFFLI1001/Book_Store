<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$success_msg = '';
$error_msg = '';

if (isset($_POST['update_profile'])) {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $phone    = mysqli_real_escape_string($conn, $_POST['phone']);

    $cek = mysqli_query($conn, "SELECT id FROM users WHERE email='$email' AND id != '$user_id'");
    if (mysqli_num_rows($cek) > 0) {
        $error_msg = "Email sudah digunakan akun lain.";
    } else {
        mysqli_query($conn, "UPDATE users SET fullname='$fullname', email='$email', phone='$phone' WHERE id='$user_id'");
        $_SESSION['username'] = $fullname; 
        $success_msg = "Profil berhasil diperbarui!";
    }
}


if (isset($_POST['change_password'])) {
    $old_pass   = $_POST['old_password'];
    $new_pass   = $_POST['new_password'];
    $conf_pass  = $_POST['confirm_password'];

    $q = mysqli_query($conn, "SELECT password FROM users WHERE id='$user_id'");
    $u = mysqli_fetch_assoc($q);

    if (!password_verify($old_pass, $u['password'])) {
        $error_msg = "Password lama tidak sesuai.";
    } elseif ($new_pass !== $conf_pass) {
        $error_msg = "Konfirmasi password tidak cocok.";
    } elseif (strlen($new_pass) < 6) {
        $error_msg = "Password baru minimal 6 karakter.";
    } else {
        $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE users SET password='$hashed' WHERE id='$user_id'");
        $success_msg = "Password berhasil diubah!";
    }
}

$q    = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($q);


$q_orders  = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE user_id='$user_id'");
$r_orders  = mysqli_fetch_assoc($q_orders);
$total_orders = $r_orders['total'] ?? 0;

$q_spend  = mysqli_query($conn, "SELECT SUM(total_amount) as total FROM orders WHERE user_id='$user_id' AND status='delivered'");
$r_spend  = mysqli_fetch_assoc($q_spend);
$total_spend = $r_spend['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Arten Book</title>
     <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/user/profile.css">
</head>
<body>

    <?php include '../include/navbar.php'; ?>

<div class="breadcrumb">
    <a href="../index.php">Beranda</a> / <span>Profil Saya</span>
</div>

<div style="max-width:1200px;margin:0 auto;padding:0 30px;">
    <?php if ($success_msg): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success_msg ?></div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= $error_msg ?></div>
    <?php endif; ?>
</div>

<div class="profile-layout">

    <aside class="profile-sidebar">
        <div class="profile-hero">
            <div class="avatar-wrap">
                <div class="avatar"><?= strtoupper(substr($user['fullname'] ?: $user['username'], 0, 1)) ?></div>
                <div class="avatar-ring"></div>
            </div>
            <div class="profile-name"><?= htmlspecialchars($user['fullname'] ?: $user['username']) ?></div>
        </div>

        <div class="profile-stats">
            <div class="stat-item">
                <span class="stat-num"><?= $total_orders ?></span>
                <span class="stat-lbl">Pesanan</span>
            </div>
            <div class="stat-item">
                <span class="stat-num">Rp <?= number_format($total_spend/1000,0) ?>K</span>
                <span class="stat-lbl">Total Belanja</span>
            </div>
        </div>

        <div class="profile-meta">
            <div class="meta-item">
                <i class="fas fa-envelope"></i>
                <div>
                    <div style="font-size:13px;"><?= htmlspecialchars($user['email']) ?></div>
                    <div class="meta-val">Email</div>
                </div>
            </div>
            <div class="meta-item">
                <i class="fas fa-phone"></i>
                <div>
                    <div style="font-size:13px;"><?= $user['phone'] ? htmlspecialchars($user['phone']) : '-' ?></div>
                    <div class="meta-val">Nomor HP</div>
                </div>
            </div>
            <div class="meta-item">
                <i class="fas fa-calendar-alt"></i>
                <div>
                    <div style="font-size:13px;"><?= date('d M Y', strtotime($user['created_at'])) ?></div>
                    <div class="meta-val">Bergabung sejak</div>
                </div>
            </div>
        </div>

        <div class="sidebar-links">
            <button class="sidebar-link active" onclick="showTab('info', this)">
                <i class="fas fa-id-card"></i> Info Akun
            </button>
            <button class="sidebar-link" onclick="showTab('edit', this)">
                <i class="fas fa-user-edit"></i> Edit Profil
            </button>
            <button class="sidebar-link" onclick="showTab('password', this)">
                <i class="fas fa-lock"></i> Ganti Password
            </button>
        </div>
    </aside>

    <div class="profile-main">
        <div class="panel-card active" id="tab-info">
            <div class="panel-header">
                <div class="panel-header-icon"><i class="fas fa-id-card"></i></div>
                <div>
                    <h2>Info Akun</h2>
                    <p>Ringkasan informasi akun kamu</p>
                </div>
            </div>
            <div class="panel-body">
                <div class="info-grid">
                    <div class="info-box">
                        <div class="info-box-label"><i class="fas fa-user"></i> Username</div>
                        <div class="info-box-val"><?= htmlspecialchars($user['username']) ?></div>
                    </div>
                    <div class="info-box">
                        <div class="info-box-label"><i class="fas fa-id-badge"></i> Nama Lengkap</div>
                        <div class="info-box-val"><?= htmlspecialchars($user['fullname'] ?: '-') ?></div>
                    </div>
                    <div class="info-box">
                        <div class="info-box-label"><i class="fas fa-envelope"></i> Email</div>
                        <div class="info-box-val"><?= htmlspecialchars($user['email']) ?></div>
                    </div>
                    <div class="info-box">
                        <div class="info-box-label"><i class="fas fa-phone"></i> No. HP</div>
                        <div class="info-box-val"><?= htmlspecialchars($user['phone'] ?: '-') ?></div>
                    </div>
                    <div class="info-box">
                        <div class="info-box-label"><i class="fas fa-shield-alt"></i> Role</div>
                        <div class="info-box-val" style="color:var(--red);"><?= ucfirst($user['role']) ?></div>
                    </div>
                    <div class="info-box">
                        <div class="info-box-label"><i class="fas fa-calendar"></i> Bergabung</div>
                        <div class="info-box-val"><?= date('d M Y', strtotime($user['created_at'])) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-card" id="tab-edit">
            <div class="panel-header">
                <div class="panel-header-icon"><i class="fas fa-user-edit"></i></div>
                <div>
                    <h2>Edit Profil</h2>
                    <p>Perbarui informasi pribadi kamu</p>
                </div>
            </div>
            <div class="panel-body">
                <form method="POST" action="">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" value="<?= htmlspecialchars($user['username']) ?>" disabled>
                            <span class="input-hint">Username tidak dapat diubah</span>
                        </div>
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="fullname" value="<?= htmlspecialchars($user['fullname']) ?>" placeholder="Nama lengkap kamu" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" placeholder="email@contoh.com" required>
                        </div>
                        <div class="form-group">
                            <label>No. HP</label>
                            <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" placeholder="08xxxxxxxxxx">
                        </div>
                    </div>
                    <button type="submit" name="update_profile" class="btn-save">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
        <div class="panel-card" id="tab-password">
            <div class="panel-header">
                <div class="panel-header-icon"><i class="fas fa-lock"></i></div>
                <div>
                    <h2>Ganti Password</h2>
                    <p>Pastikan password baru minimal 6 karakter</p>
                </div>
            </div>
            <div class="panel-body">
                <form method="POST" action="">
                    <div class="form-grid">
                        <div class="form-group full">
                            <label>Password Lama</label>
                            <input type="password" name="old_password" placeholder="Masukkan password lama" required>
                        </div>
                        <div class="form-group">
                            <label>Password Baru</label>
                            <input type="password" name="new_password" id="new_pass" placeholder="Minimal 6 karakter" required oninput="checkStrength(this.value)">
                         
                            <span class="strength-text" id="strength-text"></span>
                        </div>
                        <div class="form-group">
                            <label>Konfirmasi Password Baru</label>
                            <input type="password" name="confirm_password" id="conf_pass" placeholder="Ulangi password baru" required oninput="checkMatch()">
                            <span class="input-hint" id="match-hint"></span>
                        </div>
                    </div>
                    <button type="submit" name="change_password" class="btn-save">
                        <i class="fas fa-key"></i> Ubah Password
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

    <?php include '../include/footer.php'; ?>

<script>
    function showTab(tab, btn) {
        document.querySelectorAll('.panel-card').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.sidebar-link').forEach(el => el.classList.remove('active'));
        document.getElementById('tab-' + tab).classList.add('active');
        btn.classList.add('active');
    }

    <?php if ($success_msg || $error_msg): ?>
    window.addEventListener('DOMContentLoaded', () => {
        <?php if (isset($_POST['update_profile'])): ?>
            document.querySelector('[onclick="showTab(\'edit\', this)"]').click();
        <?php elseif (isset($_POST['change_password'])): ?>
            document.querySelector('[onclick="showTab(\'password\', this)"]').click();
        <?php endif; ?>
    });
    <?php endif; ?>

    function checkStrength(val) {
        const fill = document.getElementById('strength-fill');
        const text = document.getElementById('strength-text');
        let score = 0;
        if (val.length >= 6)  score++;
        if (val.length >= 10) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        const levels = [
            { w:'0%',   c:'#e5e7eb', l:'' },
            { w:'20%',  c:'#ef4444', l:'Sangat Lemah' },
            { w:'40%',  c:'#f97316', l:'Lemah' },
            { w:'60%',  c:'#eab308', l:'Cukup' },
            { w:'80%',  c:'#22c55e', l:'Kuat' },
            { w:'100%', c:'#16a34a', l:'Sangat Kuat' },
        ];
        fill.style.width      = levels[score].w;
        fill.style.background = levels[score].c;
        text.textContent      = levels[score].l;
        text.style.color      = levels[score].c;
    }

    function checkMatch() {
        const np   = document.getElementById('new_pass').value;
        const cp   = document.getElementById('conf_pass').value;
        const hint = document.getElementById('match-hint');
        if (cp === '') { hint.textContent = ''; return; }
        if (np === cp) {
            hint.textContent = '✓ Password cocok';
            hint.style.color = '#16a34a';
        } else {
            hint.textContent = '✗ Password tidak cocok';
            hint.style.color = '#ef4444';
        }
    }
</script>
</body>
</html>