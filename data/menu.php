<?php
// 1. Panggil file jembatan yang baru kita buat
require_once __DIR__ . '/../koneksi.php'; 

// 2. Siapkan keranjang kosong untuk menampung data dari database
$daftar_menu = [];

// 3. Tarik semua data dari tabel 'menu'
$query = mysqli_query($koneksi, "SELECT * FROM menu");

// 4. Masukkan data dari tabel satu per satu ke dalam format array lama kita 
// (supaya file index.php tidak error)
while ($row = mysqli_fetch_assoc($query)) {
    $nama_menu = $row['nama_menu'];
    $harga = $row['harga'];
    
    // Susun datanya persis seperti array manual sebelumnya
    $daftar_menu[$nama_menu] = $harga;
}
?>