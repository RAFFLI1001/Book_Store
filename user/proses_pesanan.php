<?php
session_start();
include '../config.php';

$user_id = $_SESSION['user_id'];
$nama = $_POST['nama'];
$phone = $_POST['phone'];
$alamat = $_POST['alamat'];
$metode_bayar = $_POST['metode_bayar'];
$total_bayar = $_POST['total_bayar'];

$query_cart = mysqli_query($conn, "SELECT cart.*, buku.id_buku 
                                   FROM cart 
                                   JOIN buku ON cart.id_buku = buku.id_buku 
                                   WHERE cart.user_id = '$user_id'");

while($item = mysqli_fetch_assoc($query_cart)) {
    $book_id = $item['id_buku'];

    mysqli_query($conn, "INSERT INTO orders 
        (user_id, book_id, nama, phone, alamat, metode_bayar, total_amount, status) 
        VALUES 
        ('$user_id', '$book_id', '$nama', '$phone', '$alamat', '$metode_bayar', '$total_bayar', 'pending')"
    );
}

mysqli_query($conn, "DELETE FROM cart WHERE user_id = '$user_id'");

header("Location: riwayat_pesanan.php");
exit;
?>