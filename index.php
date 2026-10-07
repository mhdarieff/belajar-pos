<?php
session_start();

if (!isset($_SESSION['status_login'])) {
    header("Location: login.php");
    exit;
}

require_once 'data/menu.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS System</title>
    
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
<body class="bg-gray-50 text-gray-900 font-sans antialiased min-h-screen p-4 md:p-8 flex flex-col md:flex-row gap-6 justify-center items-start">

    <!-- BAGIAN KIRI: DAFTAR MENU -->
    <div class="bg-white p-6 md:p-8 rounded-xl shadow-sm border border-gray-200 w-full md:w-3/5">
        
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 pb-4 border-b border-gray-100 gap-4">
            <div class="flex items-center gap-3">
                <div class="bg-blue-50 p-2 rounded-lg border border-blue-100">
                    <i data-lucide="layout-grid" class="w-5 h-5 text-blue-600"></i>
                </div>
                <h2 class="text-xl font-semibold text-gray-900 tracking-tight">Sistem Kasir</h2>
            </div>
            
            <div class="flex items-center gap-3 text-sm">
                <div class="flex items-center gap-2 text-gray-600 bg-gray-50 px-3 py-1.5 rounded-md border border-gray-200">
                    <i data-lucide="user" class="w-4 h-4"></i>
                    <span class="font-medium"><?= $_SESSION['nama_kasir'] ?></span>
                </div>
                <a href="proses/logout.php" class="flex items-center gap-1 text-gray-400 hover:text-gray-900 transition-colors font-medium">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                    Keluar
                </a>
            </div>
        </div>
        
        <!-- UI TABS (FILTER KATEGORI) -->
        <div class="flex gap-2 mb-6 overflow-x-auto pb-2 scrollbar-hide">
            <button class="filter-btn px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap bg-blue-600 text-white" data-target="semua">
                Semua
            </button>
            <?php foreach ($daftar_kategori as $kat) : ?>
                <button class="filter-btn px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap bg-gray-100 text-gray-600 hover:bg-gray-200" data-target="<?= strtolower($kat) ?>">
                    <?= ucfirst($kat) ?>
                </button>
            <?php endforeach; ?>
        </div>
        
        <!-- GRID MENU KASIR -->
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4" id="menu-container">
            <?php foreach ($daftar_menu as $item) : 
                // Upgrade Logika Ikon Berdasarkan Kategori
                $kategori_lower = strtolower($item['kategori']);
                
                if ($kategori_lower == 'makanan') {
                    $icon = 'utensils'; // Ikon sendok garpu
                } elseif ($kategori_lower == 'minuman') {
                    $icon = 'coffee';   // Ikon cangkir kopi
                } elseif ($kategori_lower == 'cemilan') {
                    $icon = 'cookie';   // Ikon biskuit/kue untuk cemilan
                } else {
                    $icon = 'package';  // Ikon default kotak kalau kategorinya baru
                }
            ?>
            <!-- Tambahkan data-kategori pada kotak menu -->
            <div class="menu-card bg-white border border-gray-200 rounded-xl p-5 flex flex-col items-center text-center hover:border-blue-200 hover:bg-blue-50/30 transition-colors" data-kategori="<?= $kategori_lower ?>">
                
                <div class="text-gray-400 mb-4">
                    <i data-lucide="<?= $icon ?>" class="w-8 h-8"></i>
                </div> 
                
                <h3 class="font-medium text-gray-900 mb-1 text-sm"><?= $item['nama'] ?></h3>
                <p class="text-gray-500 font-semibold mb-5 text-sm">Rp <?= number_format($item['harga'], 0, ',', '.') ?></p>
                
                <form action="proses/tambah.php" method="POST" class="w-full mt-auto">
                    <input type="hidden" name="nama_menu" value="<?= $item['nama'] ?>">
                    <input type="hidden" name="harga_menu" value="<?= $item['harga'] ?>">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 rounded-lg transition-colors text-sm flex justify-center items-center gap-2">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        Tambah
                    </button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- BAGIAN KANAN: KERANJANG PESANAN (Tidak berubah) -->
    <div class="bg-white p-6 md:p-8 rounded-xl shadow-sm border border-gray-200 w-full md:w-2/5">
        <div class="flex items-center gap-2 mb-6">
            <i data-lucide="shopping-bag" class="w-5 h-5 text-gray-400"></i>
            <h2 class="text-lg font-semibold text-gray-900 tracking-tight">Detail Transaksi</h2>
        </div>
        
        <?php if (isset($_SESSION['keranjang']) && count($_SESSION['keranjang']) > 0) : ?>
            
            <div class="flex flex-col gap-1 mb-6 max-h-[400px] overflow-y-auto pr-2">
            <?php 
            $total_sementara = 0;
            foreach ($_SESSION['keranjang'] as $index => $item) : 
                $subtotal_item = $item['harga'] * $item['jumlah']; 
                $total_sementara += $subtotal_item; 
            ?>
                <div class="py-3 border-b border-gray-100 last:border-0 flex justify-between items-center gap-3">
                    
                    <div class="flex-1">
                        <span class="font-medium text-gray-900 block text-sm"><?= $item['nama'] ?></span>
                        <span class="text-xs text-gray-500 mt-1 block">Rp <?= number_format($item['harga'], 0, ',', '.') ?> / porsi</span>
                    </div>
                    
                    <div class="flex flex-col items-end gap-2">
                        <span class="text-gray-900 font-semibold text-sm">Rp <?= number_format($subtotal_item, 0, ',', '.') ?></span>
                        
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-semibold text-blue-700 bg-blue-50 px-2 py-1 rounded-md border border-blue-100">
                                <?= $item['jumlah'] ?>x
                            </span>
                            <div class="flex gap-1">
                                <a href="proses/kurang.php?id=<?= $index ?>" class="text-gray-400 hover:text-gray-900 transition-colors p-1" title="Kurangi">
                                    <i data-lucide="minus" class="w-4 h-4"></i>
                                </a>
                                <a href="proses/hapus.php?id=<?= $index ?>" class="text-gray-400 hover:text-gray-900 transition-colors p-1" title="Hapus">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-xl mb-6 border border-gray-200 flex justify-between items-center">
                <span class="text-sm font-medium text-gray-600">Total Pembayaran</span>
                <span class="text-xl font-bold text-gray-900">Rp <?= number_format($total_sementara, 0, ',', '.') ?></span>
            </div>

            <form action="struk.php" method="POST">
                <label class="block text-gray-700 text-sm font-medium mb-2">Nominal Tunai</label>
                <div class="relative mb-4">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm font-medium">Rp</span>
                    </div>
                    <input type="number" name="uang_bayar" required min="<?= $total_sementara ?>" class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors" placeholder="0">
                </div>
                
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 rounded-lg transition-colors text-sm flex justify-center items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    Proses Transaksi
                </button>
            </form>
            
        <?php else : ?>
            <div class="text-center py-12">
                <div class="flex justify-center mb-3">
                    <i data-lucide="inbox" class="w-12 h-12 text-gray-300"></i>
                </div>
                <p class="text-gray-500 font-medium text-sm">Belum ada pesanan</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- SCRIPT UNTUK FITUR TABS (NO RELOAD) -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const filterBtns = document.querySelectorAll('.filter-btn');
            const menuCards = document.querySelectorAll('.menu-card');

            filterBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    // 1. Matikan warna biru di semua tombol (kembalikan ke abu-abu)
                    filterBtns.forEach(b => {
                        b.classList.remove('bg-blue-600', 'text-white');
                        b.classList.add('bg-gray-100', 'text-gray-600');
                    });

                    // 2. Nyalakan warna biru hanya di tombol yang diklik
                    btn.classList.remove('bg-gray-100', 'text-gray-600');
                    btn.classList.add('bg-blue-600', 'text-white');

                    // 3. Ambil target kategorinya (semua / minuman / makanan)
                    const target = btn.getAttribute('data-target');

                    // 4. Sembunyikan atau tampilkan menu
                    menuCards.forEach(card => {
                        const kategoriCard = card.getAttribute('data-kategori');
                        if (target === 'semua' || kategoriCard === target) {
                            card.style.display = 'flex';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            });

            // Inisialisasi ikon Lucide
            lucide.createIcons();
        });
    </script>
</body>
</html>