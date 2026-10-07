<?php
$host       = "localhost";
$user       = "root"; // username bawaan Laragon/XAMPP
$password   = "";     // password bawaan kosong
$database   = "db_warkop"; // nama database yang tadi kamu buat

// Perintah untuk menyambungkan PHP ke MySQL
$koneksi = mysqli_connect($host, $user, $password, $database);

// Cek apakah jembatannya berhasil tersambung
if (!$koneksi) {
    die("Aduh! Gagal nyambung ke database: " . mysqli_connect_error());
}
?>