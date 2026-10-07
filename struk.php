<?php
session_start();
date_default_timezone_set('Asia/Jakarta'); // Agar jam sesuai dengan waktu lokalmu

// Gembok keamanan
if (!isset($_SESSION['status_login'])) {
    header("Location: login.php");
    exit;
}

// Cek apakah ada keranjang dan input uang bayar dari halaman index
if (!isset($_SESSION['keranjang']) || empty($_SESSION['keranjang']) || !isset($_POST['uang_bayar'])) {
    header("Location: index.php");
    exit;
}

// 1. PANGGIL KONEKSI DATABASE
require_once 'koneksi.php';

$uang_bayar = (int)$_POST['uang_bayar'];
$total_belanja = 0;

// Hitung total belanja
foreach ($_SESSION['keranjang'] as $item) {
    $total_belanja += $item['harga'] * $item['jumlah'];
}

// Hitung kembalian
$kembalian = $uang_bayar - $total_belanja;

// 2. SIAPKAN DATA UNTUK DATABASE
$tanggal = date('Y-m-d H:i:s');
$nama_kasir = $_SESSION['nama_kasir'];

// 3. SIMPAN KE TABEL TRANSAKSI
$query_simpan = "INSERT INTO transaksi (tanggal, nama_kasir, total_belanja, uang_bayar, kembalian) 
                 VALUES ('$tanggal', '$nama_kasir', '$total_belanja', '$uang_bayar', '$kembalian')";
mysqli_query($koneksi, $query_simpan);

// 4. AMBIL NOMOR ID TRANSAKSI YANG BARU SAJA MASUK
$id_transaksi = mysqli_insert_id($koneksi);

// Simpan isi keranjang ke variabel lokal untuk dicetak
$daftar_pesanan = $_SESSION['keranjang'];

// Kosongkan keranjang (Ini juga mencegah data tersimpan ganda kalau user me-refresh halaman struk)
unset($_SESSION['keranjang']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran - POS System</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: { brand: '#2563eb' }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased min-h-screen flex flex-col items-center py-10 px-4">

    <!-- AREA STRUK -->
    <div class="bg-white w-full max-w-[400px] p-8 rounded-xl shadow-sm border border-gray-200 print:shadow-none print:border-none print:p-0">
        
        <div class="text-center mb-6">
            <div class="flex justify-center mb-3 print:hidden">
                <div class="bg-gray-100 p-2 rounded-lg">
                    <i data-lucide="receipt" class="w-6 h-6 text-gray-700"></i>
                </div>
            </div>
            <h2 class="text-xl font-bold tracking-tight text-gray-900">SISTEM KASIR</h2>
            <p class="text-xs text-gray-500 font-medium mt-1">Jl. Teknologi No. 1, Lhokseumawe</p>
            <p class="text-xs text-gray-500 font-medium">Telp: 0812-3456-7890</p>
        </div>

        <div class="border-t border-dashed border-gray-300 py-3 mb-3 text-xs text-gray-600 font-medium">
            <div class="flex justify-between mb-1.5">
                <span>Kasir: <?= $nama_kasir ?></span>
                <span><?= date('d/m/Y H:i') ?></span>
            </div>
            <div class="flex justify-between text-gray-500">
                <span>No. Struk:</span>
                <!-- Format ID menjadi 4 digit (contoh: TRX-0001) -->
                <span class="font-semibold text-gray-800">#TRX-<?= sprintf("%04d", $id_transaksi) ?></span>
            </div>
        </div>

        <div class="mb-4">
            <?php foreach ($daftar_pesanan as $item) : ?>
                <div class="flex justify-between items-start text-sm mb-2">
                    <div class="flex-1">
                        <span class="font-semibold text-gray-900 block"><?= $item['nama'] ?></span>
                        <span class="text-xs text-gray-500"><?= $item['jumlah'] ?> x Rp <?= number_format($item['harga'], 0, ',', '.') ?></span>
                    </div>
                    <span class="font-medium text-gray-900 text-right">
                        Rp <?= number_format($item['harga'] * $item['jumlah'], 0, ',', '.') ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="border-t border-dashed border-gray-300 pt-3 flex flex-col gap-1.5 text-sm">
            <div class="flex justify-between items-center font-bold text-gray-900 text-base">
                <span>TOTAL</span>
                <span>Rp <?= number_format($total_belanja, 0, ',', '.') ?></span>
            </div>
            <div class="flex justify-between items-center text-gray-600 mt-2">
                <span>Tunai</span>
                <span>Rp <?= number_format($uang_bayar, 0, ',', '.') ?></span>
            </div>
            <div class="flex justify-between items-center text-gray-600">
                <span>Kembali</span>
                <span>Rp <?= number_format($kembalian, 0, ',', '.') ?></span>
            </div>
        </div>

        <div class="text-center mt-8 pt-4 border-t border-dashed border-gray-300">
            <p class="text-xs font-semibold text-gray-900">Terima kasih atas kunjungan Anda!</p>
            <p class="text-xs text-gray-500 mt-1">Layanan konsumen: support@warkopos.com</p>
        </div>
    </div>

    <!-- AREA TOMBOL AKSI -->
    <div class="w-full max-w-[400px] mt-6 flex gap-3 print:hidden">
        <a href="index.php" class="flex-1 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-2.5 rounded-lg transition-colors text-sm flex justify-center items-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali
        </a>
        <button onclick="window.print()" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-lg transition-colors text-sm flex justify-center items-center gap-2">
            <i data-lucide="printer" class="w-4 h-4"></i>
            Cetak Struk
        </button>
    </div>

    <script> lucide.createIcons(); </script>
</body>
</html>