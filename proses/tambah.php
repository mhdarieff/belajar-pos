<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $nama_menu_dipesan = $_POST['nama_menu'];
    $harga_menu_dipesan = $_POST['harga_menu'];

    if (!isset($_SESSION['keranjang'])) {
        $_SESSION['keranjang'] = [];
    }

    // Cek apakah menu yang diklik sudah ada di dalam keranjang
    $sudah_ada = false;
    foreach ($_SESSION['keranjang'] as $index => $item) {
        if ($item['nama'] == $nama_menu_dipesan) {
            // Jika sudah ada, tambahkan porsinya saja!
            $_SESSION['keranjang'][$index]['jumlah'] += 1;
            $sudah_ada = true;
            break;
        }
    }

    // Jika menu belum ada di keranjang, masukkan sebagai data baru dengan jumlah 1
    if (!$sudah_ada) {
        $_SESSION['keranjang'][] = [
            'nama' => $nama_menu_dipesan,
            'harga' => $harga_menu_dipesan,
            'jumlah' => 1 // <--- Kita tambahkan data baru di sini
        ];
    }

    header("Location: ../index.php");
    exit;
}
?>