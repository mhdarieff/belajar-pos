<?php
session_start();

// Mengecek apakah ada id yang dikirim dari tombol kurang
if (isset($_GET['id'])) {
    
    $id_pesanan = $_GET['id'];
    
    // Pastikan pesanan tersebut memang ada di keranjang
    if (isset($_SESSION['keranjang'][$id_pesanan])) {
        
        // Logika Porsi: Cek apakah porsi saat ini lebih dari 1
        if ($_SESSION['keranjang'][$id_pesanan]['jumlah'] > 1) {
            
            // Jika lebih dari 1, kurangi porsinya 1
            $_SESSION['keranjang'][$id_pesanan]['jumlah'] -= 1;
            
        } else {
            // Jika porsi tersisa 1, langsung hapus menu tersebut dari keranjang
            unset($_SESSION['keranjang'][$id_pesanan]);
        }
    }
}

// Kembali ke halaman kasir
header("Location: ../index.php");
exit;
?>