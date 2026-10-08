<?php
// Menyambungkan PHP dengan database MySQL
$koneksi = mysqli_connect("localhost", "root", "", "db_warkop");

// Pesan error jika database gagal terhubung atau nama database salah
if (mysqli_connect_errno()) {
    echo "Koneksi database gagal: " . mysqli_connect_error();
    exit();
}
?>