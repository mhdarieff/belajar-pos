<?php
require_once __DIR__ . '/../koneksi.php'; 

$daftar_menu = [];
$daftar_kategori = []; // Untuk menampung nama-nama Tab Kategori

$query = mysqli_query($koneksi, "SELECT * FROM menu");

while ($row = mysqli_fetch_assoc($query)) {
    // Masukkan semua data (nama, harga, kategori) ke array
    $daftar_menu[] = [
        'nama'     => $row['nama_menu'],
        'harga'    => $row['harga'],
        'kategori' => $row['kategori']
    ];
    
    // Kumpulkan kategori unik untuk dijadikan tombol Tab
    $kategori = $row['kategori'];
    if (!empty($kategori) && !in_array($kategori, $daftar_kategori)) {
        $daftar_kategori[] = $kategori;
    }
}
?>