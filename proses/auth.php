<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $username_input = $_POST['username'];
    $password_input = $_POST['password'];

    // Simulasi daftar akun (Bisa login sebagai Arief atau Despal)
    $akun_terdaftar = [
        'arief' => 'kasir123',
        'despal' => 'kasir123'
    ];

    // Cek apakah username ada di daftar, DAN passwordnya cocok
    if (array_key_exists($username_input, $akun_terdaftar) && $akun_terdaftar[$username_input] == $password_input) {
        
        // Buat TIKET MASUK (Session)
        $_SESSION['status_login'] = true;
        $_SESSION['nama_kasir'] = ucfirst($username_input); // Mengubah 'arief' jadi 'Arief'
        
        // Arahkan ke aplikasi kasir
        header("Location: ../index.php");
        exit;
        
    } else {
        // Jika salah, tendang balik ke halaman login sambil membawa pesan error
        header("Location: ../login.php?pesan=gagal");
        exit;
    }
}
?>