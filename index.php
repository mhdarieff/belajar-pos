<?php
session_start();
// Memanggil data menu dari folder sebelah!
require_once 'data/menu.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kasir Warkop BelajarPOS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans p-4 md:p-10 flex flex-col md:flex-row gap-6 justify-center items-start min-h-screen">

    <div class="bg-white p-6 rounded-2xl shadow-lg w-full md:w-3/5 border border-gray-200">
        <h2 class="text-2xl font-bold text-orange-600 mb-5 flex items-center gap-2">
            ☕ Menu Warkop BelajarPOS
        </h2>
        
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-orange-500 text-white">
                    <th class="p-3 rounded-tl-lg">Nama Menu</th>
                    <th class="p-3">Harga</th>
                    <th class="p-3 rounded-tr-lg text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($daftar_menu as $nama => $harga) : ?>
                <tr class="border-b hover:bg-orange-50 transition duration-150">
                    <td class="p-3 font-semibold"><?= $nama ?></td>
                    <td class="p-3 text-gray-600">Rp <?= number_format($harga, 0, ',', '.') ?></td>
                    <td class="p-3 text-center">
                        <!-- PERHATIKAN: Form sekarang diarahkan ke folder proses/tambah.php -->
                        <form action="proses/tambah.php" method="POST" class="m-0">
                            <input type="hidden" name="nama_menu" value="<?= $nama ?>">
                            <input type="hidden" name="harga_menu" value="<?= $harga ?>">
                            <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-1.5 px-4 rounded-lg shadow-sm transition">
                                + Tambah
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- BAGIAN KANAN: KERANJANG PESANAN -->
    <div class="bg-orange-50 p-6 rounded-2xl shadow-lg border-2 border-dashed border-orange-300 w-full md:w-2/5">
        <h2 class="text-2xl font-bold text-orange-600 mb-5">🛒 Keranjang Saat Ini</h2>
        
        <?php if (isset($_SESSION['keranjang']) && count($_SESSION['keranjang']) > 0) : ?>
            
            <!-- KOTAK PEMBUNGKUS SEMUA PESANAN -->
            <div class="flex flex-col gap-3 mb-4">
            
            <?php 
            $total_sementara = 0;
            foreach ($_SESSION['keranjang'] as $index => $item) : 
                $subtotal_item = $item['harga'] * $item['jumlah']; 
                $total_sementara += $subtotal_item; 
            ?>
                <!-- BARIS SETIAP PESANAN -->
                <div class="flex justify-between items-center border-b border-orange-200 pb-2 gap-2">
                    
                    <!-- Kiri: Nama dan Porsi -->
                    <div class="flex-1 leading-tight">
                        <span class="font-medium text-gray-700 block md:inline"><?= $item['nama'] ?></span>
                        <span class="text-sm font-bold text-orange-500 md:ml-1">x<?= $item['jumlah'] ?></span>
                    </div>
                    
                    <!-- Kanan: Harga dan Tombol -->
                    <div class="flex items-center gap-2 justify-end">
                        <span class="text-gray-600 whitespace-nowrap mr-1">Rp <?= number_format($subtotal_item, 0, ',', '.') ?></span>
                        
                        <a href="proses/kurang.php?id=<?= $index ?>" class="text-yellow-600 font-bold bg-yellow-100 hover:bg-yellow-200 rounded-full w-7 h-7 flex items-center justify-center transition shrink-0" title="Kurangi 1 Porsi">
                            -
                        </a>
                        
                        <a href="proses/hapus.php?id=<?= $index ?>" class="text-red-500 font-bold bg-red-100 hover:bg-red-200 rounded-full w-7 h-7 flex items-center justify-center transition shrink-0" title="Hapus semua porsi">
                            ✕
                        </a>
                    </div>

                </div> <!-- Akhir dari Baris Pesanan -->
                
            <?php endforeach; ?>
            </div> <!-- Akhir dari Kotak Pembungkus -->
            
            <div class="pt-3 border-t-2 border-orange-300 flex justify-between items-center">
                <span class="text-lg font-bold text-gray-700">Total:</span>
                <span class="text-2xl font-extrabold text-orange-600">Rp <?= number_format($total_sementara, 0, ',', '.') ?></span>
            </div>

           <!-- Form Input Uang Pembayaran -->
<form action="struk.php" method="POST" class="mt-6 border-t-2 border-orange-300 pt-4">
    <label class="block text-gray-700 text-sm font-bold mb-2">Uang Tunai Pelanggan (Rp):</label>
    <input type="number" name="uang_bayar" required min="<?= $total_sementara ?>" class="w-full px-4 py-2 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200" placeholder="Contoh: 100000">
    
    <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 rounded-xl transition shadow-md">
        Cetak Struk & Bayar ➔
    </button>
</form>
            
        <?php else : ?>
            <div class="text-center py-10">
                <span class="text-4xl">🧾</span>
                <p class="text-gray-400 italic mt-3">Belum ada pesanan.</p>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>