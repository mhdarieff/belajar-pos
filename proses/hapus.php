<?php
// Wajib ada session_start()
session_start();

// Mengecek apakah id dikirim dari tombol X
if (isset($_GET['id'])) {
    
    $id_pesanan = $_GET['id'];
    
    // Mengecek apakah pesanan dengan nomor id tersebut ada di memori keranjang
    if (isset($_SESSION['keranjang'][$id_pesanan])) {
        
        // Hapus pesanan tersebut!
        unset($_SESSION['keranjang'][$id_pesanan]);
    }
}

// Tendang balik ke halaman depan
header("Location: ../index.php");
exit;
?>