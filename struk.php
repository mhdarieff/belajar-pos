<?php
session_start();

// 1. KEAMANAN: Jika tidak ada pesanan, kembalikan ke halaman kasir
if (!isset($_SESSION['keranjang']) || count($_SESSION['keranjang']) == 0) {
    header("Location: index.php");
    exit;
}

// 2. AMBIL DATA PESANAN DARI SESSION
$pesanan = $_SESSION['keranjang'];

// 3. TANGKAP UANG BAYAR DARI FORM (Jika tidak diisi, anggap 0)
$uang_bayar = isset($_POST['uang_bayar']) ? $_POST['uang_bayar'] : 0;

// 4. RESET KERANJANG AGAR KOSONG
unset($_SESSION['keranjang']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Pembayaran Warkop</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-200 flex justify-center items-center min-h-screen p-4">

    <div class="bg-white p-8 rounded-lg shadow-2xl w-full max-w-sm border-t-8 border-gray-800">
        
        <div class="text-center mb-6">
            <h1 class="text-2xl font-black text-gray-800 tracking-wider">WARKOP BELAJARPOS</h1>
            <p class="text-gray-500 text-sm">Jl. Lintas Aceh, Lhokseumawe</p>
            <p class="text-gray-400 text-xs mt-1">Waktu: <?= date('d-m-Y H:i') ?></p>
        </div>

        <div class="border-t-2 border-dashed border-gray-300 py-4 mb-4">
            <?php 
            $subtotal = 0;
            foreach ($pesanan as $item) : 
                $subtotal_item = $item['harga'] * $item['jumlah'];
                $subtotal += $subtotal_item;
            ?>
                <div class="flex justify-between text-gray-700 font-medium mb-1">
                    <span><?= $item['nama'] ?> (x<?= $item['jumlah'] ?>)</span>
                    <span><?= number_format($subtotal_item, 0, ',', '.') ?></span>
                </div>
            <?php endforeach; ?>
        </div>

        <?php
            // Logika Matematika
            $ppn = $subtotal * 0.10; 
            $total_bayar = $subtotal + $ppn;
            $kembalian = $uang_bayar - $total_bayar;
        ?>

        <div class="border-t-2 border-dashed border-gray-300 pt-4 mb-6">
            <div class="flex justify-between text-gray-500 text-sm mb-1">
                <span>Subtotal</span>
                <span>Rp <?= number_format($subtotal, 0, ',', '.') ?></span>
            </div>
            <div class="flex justify-between text-gray-500 text-sm mb-3">
                <span>PPN (10%)</span>
                <span>Rp <?= number_format($ppn, 0, ',', '.') ?></span>
            </div>
            
            <div class="flex justify-between text-xl font-bold text-gray-900 mt-2 pt-3 border-t border-gray-800">
                <span>TOTAL</span>
                <span>Rp <?= number_format($total_bayar, 0, ',', '.') ?></span>
            </div>

            <div class="flex justify-between text-gray-700 font-medium mt-4 pt-4 border-t-2 border-dashed border-gray-300">
                <span>Tunai</span>
                <span>Rp <?= number_format($uang_bayar, 0, ',', '.') ?></span>
            </div>
            <div class="flex justify-between text-lg font-bold text-emerald-600 mt-1">
                <span>Kembali</span>
                <span>Rp <?= number_format($kembalian, 0, ',', '.') ?></span>
            </div>
        </div>

        <div class="text-center mt-8">
            <p class="text-gray-500 text-sm italic">Terima kasih atas kunjungan Anda!</p>
            <a href="index.php" class="inline-block mt-8 bg-gray-800 hover:bg-black text-white font-semibold py-3 px-6 rounded-lg transition w-full">
                Selesai & Buat Pesanan Baru
            </a>
        </div>
        
    </div>

</body>
</html>