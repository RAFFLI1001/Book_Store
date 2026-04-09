<?php
session_start();
include '../config.php';

$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subjek = trim($_POST['subjek'] ?? '');
    $pesan = trim($_POST['pesan'] ?? '');

    if (empty($nama))
        $errors[] = 'Nama tidak boleh kosong.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors[] = 'Email tidak valid.';
    if (empty($subjek))
        $errors[] = 'Subjek tidak boleh kosong.';
    if (empty($pesan))
        $errors[] = 'Pesan tidak boleh kosong.';

}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hubungi Kami - Arten Book</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/pages/contact.css">
</head>

<body>
    <?php include '../include/navbar.php'; ?>

    <section class="contact-hero">
        <h1><i class="fas fa-headset"></i> Hubungi Kami</h1>
        <p>Ada pertanyaan, saran, atau kendala? Tim kami siap membantu Anda dengan senang hati.</p>
    </section>
    <div class="contact-wrapper">
        <div class="contact-info">
            <h2>Informasi Kontak</h2>
            <p>Kami beroperasi setiap hari Senin–Sabtu, pukul 08.00–17.00 WIB. Hubungi kami melalui saluran berikut:</p>

            <div class="info-card">
                <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                <div class="info-text">
                    <h4>Alamat</h4>
                    <p>Jl. Buku Indah No. 12, Kota Jakarta<br>DKI Jakarta, 10110</p>
                </div>
            </div>

            <div class="info-card">
                <div class="info-icon"><i class="fas fa-phone-alt"></i></div>
                <div class="info-text">
                    <h4>Telepon / WhatsApp</h4>
                    <p>+62 858-1007-7475</p>
                </div>
            </div>

            <div class="info-card">
                <div class="info-icon"><i class="fas fa-envelope"></i></div>
                <div class="info-text">
                    <h4>Email</h4>
                    <p>@artenbook.id</p>
                </div>
            </div>
        </div>

        <div class="contact-form-box">
            <h2><i class="fas fa-paper-plane" style="color:#3498db;margin-right:8px;"></i>Kirim Pesan</h2>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle fa-lg"></i>
                    <div>
                        <strong>Pesan terkirim!</strong><br>
                        Terima kasih telah menghubungi kami. Tim kami akan segera membalas pesan Anda.
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle fa-lg"></i>
                    <div>
                        <strong>Terdapat kesalahan:</strong>
                        <ul>
                            <?php foreach ($errors as $e): ?>
                                <li><?= htmlspecialchars($e) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <form id="contactForm">
                <div class="form-row">
                    <div class="form-group">
                        <label>Nama Lengkap <span>*</span></label>
                        <input type="text" name="nama" placeholder="John Doe"
                            value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Alamat Email <span>*</span></label>
                        <input type="email" name="email" placeholder="email@contoh.com"
                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Subjek <span>*</span></label>
                    <select name="subjek" required>
                        <option value="" disabled <?= empty($_POST['subjek']) ? 'selected' : '' ?>> Pilih topik 
                        </option>
                        <option value="Pemesanan" <?= (($_POST['subjek'] ?? '') === 'Pemesanan') ? 'selected' : '' ?>>
                            Pemesanan Buku</option>
                        <option value="Pengiriman" <?= (($_POST['subjek'] ?? '') === 'Pengiriman') ? 'selected' : '' ?>>
                            Status Pengiriman</option>
                        <option value="Pembayaran" <?= (($_POST['subjek'] ?? '') === 'Pembayaran') ? 'selected' : '' ?>>
                            Pembayaran</option>
                        <option value="Retur" <?= (($_POST['subjek'] ?? '') === 'Retur') ? 'selected' : '' ?>>Pengembalian
                            / Retur</option>
                        <option value="Kerjasama" <?= (($_POST['subjek'] ?? '') === 'Kerjasama') ? 'selected' : '' ?>>
                            Kerjasama / Partnership</option>
                        <option value="Lainnya" <?= (($_POST['subjek'] ?? '') === 'Lainnya') ? 'selected' : '' ?>>Lainnya
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Pesan <span>*</span></label>
                    <textarea name="pesan" placeholder="Tuliskan pesan Anda di sini..."
                        required><?= htmlspecialchars($_POST['pesan'] ?? '') ?></textarea>
                </div>

                <button type="button" onclick="kirimWA()" class="btn-kirim">
                    <i class="fab fa-whatsapp"></i> Kirim via WhatsApp
                </button>
            </form>
        </div>
    </div>

    <?php include '../include/footer.php'; ?>

    <script>
        function toggleFaq(btn) {
            const item = btn.closest('.faq-item');
            item.classList.toggle('open');
        }

        function kirimWA() {
            const nama = document.querySelector('[name="nama"]').value.trim();
            const email = document.querySelector('[name="email"]').value.trim();
            const subjek = document.querySelector('[name="subjek"]').value;
            const pesan = document.querySelector('[name="pesan"]').value.trim();

            if (!nama) { alert('Nama tidak boleh kosong.'); return; }
            if (!email) { alert('Email tidak boleh kosong.'); return; }
            if (!subjek) { alert('Pilih subjek terlebih dahulu.'); return; }
            if (!pesan) { alert('Pesan tidak boleh kosong.'); return; }

            const nomorWA = '6285810077475';

            const teks =
                `Halo Arten Book! 👋

*Nama:* ${nama}
*Email:* ${email}
*Subjek:* ${subjek}

*Pesan:*
${pesan}`;

            const url = `https://wa.me/${nomorWA}?text=${encodeURIComponent(teks)}`;
            window.open(url, '_blank');
        }
    </script>
</body>

</html>