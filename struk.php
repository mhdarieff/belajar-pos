<?php
session_start();
require_once 'koneksi.php';

// Cek apakah ada keranjang dan uang bayar
if (!isset($_SESSION['keranjang']) || empty($_SESSION['keranjang']) || !isset($_POST['uang_bayar'])) {
    header("Location: index.php");
    exit;
}

// Hitung total belanja
$total_belanja = 0;
foreach ($_SESSION['keranjang'] as $item) {
    $total_belanja += $item['harga'] * $item['jumlah'];
}

$uang_bayar = $_POST['uang_bayar'];
$kembalian = $uang_bayar - $total_belanja;

// Jika uang kurang, batalkan
if ($kembalian < 0) {
    echo "<script>alert('Uang bayar tidak cukup!'); window.location.href='index.php';</script>";
    exit;
}

// 1. SIMPAN KE TABEL TRANSAKSI (Utama)
$tanggal = date('Y-m-d H:i:s');
$nama_kasir = $_SESSION['nama_kasir']; 

// BUGS FIXED: Masukkan uang_bayar, kembalian, dan nama_kasir ke database
mysqli_query($koneksi, "INSERT INTO transaksi (tanggal, total_belanja, uang_bayar, kembalian, nama_kasir) 
                        VALUES ('$tanggal', '$total_belanja', '$uang_bayar', '$kembalian', '$nama_kasir')");

// 2. DAPATKAN ID TRANSAKSI YANG BARU SAJA MASUK
$id_trx_baru = mysqli_insert_id($koneksi);

// 3. BONGKAR KERANJANG DAN SIMPAN KE TABEL DETAIL_TRANSAKSI
foreach ($_SESSION['keranjang'] as $item) {
    $nama = $item['nama'];
    $harga = $item['harga'];
    $qty = $item['jumlah'];
    
    // Masukkan setiap menu ke database
    mysqli_query($koneksi, "INSERT INTO detail_transaksi (id_transaksi, nama_menu, harga, jumlah) 
                            VALUES ('$id_trx_baru', '$nama', '$harga', '$qty')");
}

// 4. AMANKAN DATA UNTUK DICETAK, LALU KOSONGKAN KERANJANG
$struk_pesanan = $_SESSION['keranjang'];
unset($_SESSION['keranjang']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran - WarkopOS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        /* Menggunakan font ala mesin kasir (monospaced) */
        .font-struk { font-family: 'Space Mono', monospace; }
        /* Garis putus-putus khas struk thermal */
        .garis-struk { border-bottom: 2px dashed #cbd5e1; margin: 16px 0; }
        /* Sembunyikan tombol saat struk di-print */
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
            .struk-container { box-shadow: none; border: none; margin: 0; padding: 0; width: 100%; max-width: 100%; }
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-6">

    <div class="max-w-sm w-full">
        
        <!-- KERTAS STRUK -->
        <div class="struk-container bg-white p-8 rounded-t-xl shadow-lg border-t-4 border-blue-600 relative overflow-hidden">
            
            <!-- Ornamen Ujung Kertas Bergerigi (Opsional, efek visual) -->
            <div class="absolute top-0 left-0 right-0 h-1 flex justify-around">
                <div class="w-2 h-2 bg-gray-100 rounded-full -mt-1"></div>
                <div class="w-2 h-2 bg-gray-100 rounded-full -mt-1"></div>
                <div class="w-2 h-2 bg-gray-100 rounded-full -mt-1"></div>
            </div>

            <!-- HEADER STRUK -->
            <div class="text-center mb-6">
                <h1 class="font-struk font-bold text-2xl text-gray-900">WARKOP OS</h1>
                <p class="font-struk text-xs text-gray-500 mt-1">Jl. Mawar No. 123, Kota Kopi</p>
                <p class="font-struk text-xs text-gray-500">Telp: 0812-3456-7890</p>
            </div>

            <div class="garis-struk"></div>

            <!-- INFO TRANSAKSI -->
            <div class="flex justify-between font-struk text-xs text-gray-600 mb-2">
                <span>Waktu:</span>
                <span><?= date('d/m/Y H:i') ?></span>
            </div>
            <div class="flex justify-between font-struk text-xs text-gray-600 mb-2">
                <span>Kasir:</span>
                <span><?= $_SESSION['nama_kasir'] ?></span>
            </div>
            <div class="flex justify-between font-struk text-xs text-gray-600">
                <span>No. TRX:</span>
                <span class="font-bold">#TRX-<?= str_pad($id_trx_baru, 5, '0', STR_PAD_LEFT) ?></span>
            </div>

            <div class="garis-struk"></div>

            <!-- RINCIAN PESANAN -->
            <div class="mb-4">
                <?php foreach ($struk_pesanan as $item) : 
                    $sub = $item['harga'] * $item['jumlah'];
                ?>
                <div class="font-struk text-sm text-gray-800 mb-2">
                    <div class="font-bold uppercase"><?= $item['nama'] ?></div>
                    <div class="flex justify-between text-xs mt-0.5 text-gray-600">
                        <span><?= $item['jumlah'] ?> x <?= number_format($item['harga'], 0, ',', '.') ?></span>
                        <span class="font-semibold text-gray-900"><?= number_format($sub, 0, ',', '.') ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="garis-struk"></div>

            <!-- TOTAL & PEMBAYARAN -->
            <div class="flex justify-between font-struk text-sm text-gray-800 font-bold mb-1">
                <span>TOTAL:</span>
                <span>Rp <?= number_format($total_belanja, 0, ',', '.') ?></span>
            </div>
            <div class="flex justify-between font-struk text-xs text-gray-600 mb-1">
                <span>TUNAI:</span>
                <span>Rp <?= number_format($uang_bayar, 0, ',', '.') ?></span>
            </div>
            <div class="flex justify-between font-struk text-xs text-gray-600">
                <span>KEMBALI:</span>
                <span>Rp <?= number_format($kembalian, 0, ',', '.') ?></span>
            </div>

            <div class="garis-struk"></div>

            <!-- FOOTER STRUK -->
            <div class="text-center mt-6">
                <p class="font-struk text-xs text-gray-800 font-bold mb-1">TERIMA KASIH!</p>
                <p class="font-struk text-[10px] text-gray-400">Barang yang sudah dibeli tidak dapat ditukar/dikembalikan.</p>
            </div>
        </div>
        
        <!-- Bagian bawah kertas bergerigi (efek visual tailwind) -->
        <div class="bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4IiBoZWlnaHQ9IjgiPgo8cGF0aCBkPSJNMCA4IEwgNCAwIEwgOCA4IFoiIGZpbGw9IiNmZmYiIC8+Cjwvc3ZnPg==')] h-2 w-full shadow-lg"></div>

        <!-- TOMBOL AKSI (Disembunyikan saat di-print) -->
        <div class="no-print flex gap-3 mt-6">
            <button onclick="window.print()" class="flex-1 bg-white border border-gray-200 text-gray-700 font-bold py-3 px-4 rounded-xl shadow-sm hover:bg-gray-50 hover:text-blue-600 transition-colors flex justify-center items-center gap-2 text-sm">
                <i data-lucide="printer" class="w-4 h-4"></i> Cetak
            </button>
            <a href="index.php" class="flex-1 bg-blue-600 text-white font-bold py-3 px-4 rounded-xl shadow-sm hover:bg-blue-700 transition-colors flex justify-center items-center gap-2 text-sm">
                Selesai <i data-lucide="check-circle" class="w-4 h-4"></i>
            </a>
        </div>
        
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>