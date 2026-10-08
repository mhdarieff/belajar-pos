<?php
session_start();
if (!isset($_SESSION['status_login'])) {
    header("Location: ../login.php");
    exit;
}

require_once '../koneksi.php';

if (isset($_GET['id'])) {
    $id_trx = $_GET['id'];
    
    // 1. Hapus rincian menu di tabel detail_transaksi terlebih dahulu
    mysqli_query($koneksi, "DELETE FROM detail_transaksi WHERE id_transaksi = '$id_trx'");
    
    // 2. Baru hapus riwayat utamanya di tabel transaksi
    mysqli_query($koneksi, "DELETE FROM transaksi WHERE id_transaksi = '$id_trx'");
}

// Kembalikan ke halaman riwayat setelah selesai menghapus
header("Location: ../riwayat.php");
exit;
?>