<?php
include '../config.php';

if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $cek_user = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    if (mysqli_num_rows($cek_user) > 0) {
        $error = "Username sudah digunakan!";
    } else {
        $query = "INSERT INTO users (username, password, email, fullname, phone, role) 
        VALUES ('$username', '$password', '$email', '$fullname', '$phone', 'user')";
        
        if (mysqli_query($conn, $query)) { 
            echo "<script>alert('Pendaftaran Berhasil!'); window.location='../login.php';</script>"; 
        } else {
            $error = "Terjadi kesalahan!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register | Arten Book</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link rel="stylesheet" href="../assets/css/user/register.css">
</head>

<body>

<div class="register-container">

    <div class="register-header">
        <h2>Buat Akun</h2>
        <p>Daftar untuk mulai menggunakan Arten Book</p>
    </div>

    <?php if(isset($error)): ?>
        <div class="error-message"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">

        <div class="input-group">
            <label>Username</label>
            <input type="text" name="username" required>
        </div>

        <div class="input-group">
            <label>Nama Lengkap</label>
            <input type="text" name="fullname" required>
        </div>

        <div class="input-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>

        <div class="input-group">
            <label>No. Telepon</label>
            <input type="text" name="phone" required>
        </div>

        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" id="password" required>
            <i class="fas fa-eye toggle-password" id="togglePassword"></i>
        </div>

        <button type="submit" name="register" class="register-btn">
            <i class="fas fa-user-plus"></i> Daftar
        </button>
    </form>

    <div class="login-link">
        Sudah punya akun? <a href="../login.php">Masuk</a>
    </div>

</div>

<script>
const toggle = document.getElementById('togglePassword');
const pass = document.getElementById('password');

toggle.addEventListener('click', function(){
    const type = pass.type === 'password' ? 'text' : 'password';
    pass.type = type;
    this.classList.toggle('fa-eye');
    this.classList.toggle('fa-eye-slash');
});
</script>

</body>
</html>