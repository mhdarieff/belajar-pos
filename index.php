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
    <title>WarkopOS - Terminal Kasir</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
    <style>
        .menu-card { display: none; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        input[type="number"]::-webkit-inner-spin-button, 
        input[type="number"]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
    </style>
</head>
<body class="bg-gray-100 text-gray-900 font-sans antialiased h-screen flex overflow-hidden">

    <!-- 1. SIDEBAR NAVIGASI KIRI -->
    <nav class="w-[260px] bg-gray-900 text-gray-300 flex flex-col shrink-0 z-30 shadow-2xl relative">
        <div class="h-16 flex items-center px-6 border-b border-gray-800 bg-gray-950/50">
            <div class="flex items-center gap-3">
                <div class="bg-blue-600 p-2 rounded-lg text-white shadow-sm shadow-blue-600/20">
                    <i data-lucide="coffee" class="w-5 h-5"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold tracking-tight text-white leading-none">WarkopOS</h1>
                </div>
            </div>
        </div>
        
        <div class="p-4 flex-1 overflow-y-auto flex flex-col gap-1">
            <p class="px-3 text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2 mt-2">Workspace</p>
            <a href="index.php" class="flex items-center gap-3 px-3 py-3 bg-blue-600 text-white rounded-xl font-semibold transition-colors shadow-sm shadow-blue-600/20">
                <i data-lucide="monitor" class="w-5 h-5"></i>
                Terminal Kasir
            </a>
            
            <p class="px-3 text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2 mt-6">Manajemen Data</p>
            <a href="dashboard.php" class="flex items-center gap-3 px-3 py-3 text-gray-400 hover:text-white hover:bg-gray-800 rounded-xl font-medium transition-all group">
                <i data-lucide="bar-chart-2" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                Backoffice
            </a>
            <a href="riwayat.php" class="flex items-center gap-3 px-3 py-3 text-gray-400 hover:text-white hover:bg-gray-800 rounded-xl font-medium transition-all group">
                <i data-lucide="history" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                Riwayat Transaksi
            </a>
        </div>

        <div class="p-4 border-t border-gray-800 bg-gray-950/30">
            <div class="flex items-center gap-3 px-2 py-2 mb-3 bg-gray-800/50 rounded-lg border border-gray-700/50">
                <div class="w-9 h-9 rounded-md bg-gray-700 flex items-center justify-center text-white font-bold text-sm">
                    <?= strtoupper(substr($_SESSION['nama_kasir'], 0, 1)) ?>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-sm text-white truncate"><?= $_SESSION['nama_kasir'] ?></p>
                    <p class="text-[11px] text-gray-400 font-medium">Kasir Aktif</p>
                </div>
            </div>
            <a href="proses/logout.php" onclick="sessionStorage.clear()" class="flex items-center justify-center gap-2 w-full bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white py-2.5 rounded-xl transition-all font-semibold text-sm border border-red-500/20 hover:border-transparent">
                <i data-lucide="log-out" class="w-4 h-4"></i>
                Tutup Shift
            </a>
        </div>
    </nav>

    <!-- 2. AREA KONTEN TENGAH -->
    <main class="flex-1 flex flex-col bg-[#f8fafc] relative z-10">
        
        <header class="h-16 bg-white border-b border-gray-200 flex items-center px-6 shrink-0 z-20 shadow-sm relative">
            <h2 class="text-lg font-bold text-gray-900 tracking-tight">Pilih Pesanan</h2>
        </header>

        <!-- BUGS FIXED: Toolbar ditinggikan Z-Index-nya menjadi z-40 relative agar lacinya tidak tenggelam -->
        <div class="px-6 py-4 bg-white/60 backdrop-blur-md border-b border-gray-200 shrink-0 z-40 relative">
            <!-- CUSTOM DROPDOWN BUTTON (Laci) -->
            <div class="relative w-full md:w-64">
                <button id="dropdown-btn" class="w-full flex items-center justify-between bg-white border border-gray-300 hover:border-blue-500 text-gray-700 px-4 py-2.5 rounded-xl shadow-sm transition-all focus:outline-none focus:ring-4 focus:ring-blue-500/10 group">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="list-filter" class="w-4.5 h-4.5 text-blue-600"></i>
                        <span id="dropdown-label" class="text-sm font-semibold text-gray-700">Pilih Kategori...</span>
                    </div>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 transition-transform duration-300" id="dropdown-icon"></i>
                </button>

                <!-- LACI KATEGORI -->
                <div id="dropdown-menu" class="absolute left-0 top-full mt-2 w-full bg-white border border-gray-200 rounded-xl shadow-xl shadow-gray-200/50 py-2 hidden flex-col z-50 transform origin-top transition-all duration-200">
                    
                    <button class="filter-btn w-full text-left px-4 py-2.5 text-sm font-medium hover:bg-blue-50 hover:text-blue-700 transition-colors flex items-center gap-3 text-gray-600" data-target="semua" data-label="Semua Menu">
                        <i data-lucide="grid-3x3" class="w-4 h-4 text-gray-400"></i> Semua Menu
                    </button>
                    
                    <div class="h-px bg-gray-100 my-1 mx-4"></div>
                    
                    <?php foreach ($daftar_kategori as $kat) : 
                        $kat_lower = strtolower($kat);
                        $kat_icon = ($kat_lower == 'makanan') ? 'utensils' : (($kat_lower == 'minuman') ? 'coffee' : (($kat_lower == 'cemilan') ? 'cookie' : 'package'));
                    ?>
                        <button class="filter-btn w-full text-left px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-blue-50 hover:text-blue-700 transition-colors flex items-center gap-3" data-target="<?= $kat_lower ?>" data-label="<?= ucfirst($kat) ?>">
                            <i data-lucide="<?= $kat_icon ?>" class="w-4 h-4 text-gray-400"></i> <?= ucfirst($kat) ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- AREA GRID MENU -->
        <div class="flex-1 overflow-y-auto p-6 relative">
            
            <!-- BLANK STATE / WELCOME SCREEN -->
            <div id="welcome-state" class="absolute inset-0 flex flex-col items-center justify-center bg-[#f8fafc] z-10">
                <div class="bg-white p-6 rounded-full shadow-sm border border-gray-100 mb-6 group hover:scale-105 transition-transform duration-300">
                    <i data-lucide="store" class="w-16 h-16 text-blue-600"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2 tracking-tight">Terminal Kasir Siap</h3>
                <p class="text-gray-500 font-medium max-w-sm text-center">Buka laci kategori di atas untuk mulai memuat daftar menu.</p>
            </div>

            <!-- KARTU MENU (Awalnya Sembunyi) -->
            <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4" id="menu-container" style="display: none;">
                <?php foreach ($daftar_menu as $item) : 
                    $kategori_lower = strtolower($item['kategori']);
                    $icon = ($kategori_lower == 'makanan') ? 'utensils' : (($kategori_lower == 'minuman') ? 'coffee' : (($kategori_lower == 'cemilan') ? 'cookie' : 'package'));
                ?>
                <div class="menu-card bg-white border border-gray-200 rounded-xl p-4 flex flex-col hover:border-blue-500 hover:shadow-md transition-all duration-200 group" data-kategori="<?= $kategori_lower ?>">
                    <div class="flex justify-between items-start mb-4">
                        <div class="bg-gray-50 p-3 rounded-lg text-gray-400 group-hover:bg-blue-50 group-hover:text-blue-600 transition-colors">
                            <i data-lucide="<?= $icon ?>" class="w-6 h-6"></i>
                        </div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider bg-gray-100 px-2 py-1 rounded-md"><?= ucfirst($kategori_lower) ?></span>
                    </div>
                    <div class="mt-auto">
                        <h3 class="font-bold text-gray-900 leading-tight mb-1"><?= $item['nama'] ?></h3>
                        <p class="text-blue-600 font-bold text-sm mb-4">Rp <?= number_format($item['harga'], 0, ',', '.') ?></p>
                        <form action="proses/tambah.php" method="POST" class="w-full form-tambah">
                            <input type="hidden" name="nama_menu" value="<?= $item['nama'] ?>">
                            <input type="hidden" name="harga_menu" value="<?= $item['harga'] ?>">
                            <button type="submit" class="w-full bg-gray-50 hover:bg-blue-600 text-gray-700 hover:text-white border border-gray-200 hover:border-blue-600 font-semibold py-2 rounded-lg transition-all text-sm flex justify-center items-center gap-1.5 shadow-sm">
                                <i data-lucide="plus" class="w-4 h-4"></i> Tambah
                            </button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="h-8"></div> 
        </div>
    </main>

    <!-- 3. PANEL KANAN: SIDEBAR KERANJANG -->
    <aside class="w-[380px] bg-white border-l border-gray-200 flex flex-col shrink-0 z-20 shadow-[-4px_0_15px_rgba(0,0,0,0.02)]" id="area-keranjang">
        <div class="p-5 border-b border-gray-100 shrink-0 flex items-center justify-between bg-gray-50/50">
            <h2 class="text-lg font-bold text-gray-900 tracking-tight">Tagihan Pesanan</h2>
            <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2.5 py-1 rounded-full">
                <?= isset($_SESSION['keranjang']) ? count($_SESSION['keranjang']) : 0 ?> Item
            </span>
        </div>
        
        <div class="flex-1 overflow-y-auto p-5">
            <?php if (isset($_SESSION['keranjang']) && count($_SESSION['keranjang']) > 0) : ?>
                <div class="flex flex-col gap-3">
                <?php 
                $total_sementara = 0;
                foreach ($_SESSION['keranjang'] as $index => $item) : 
                    $subtotal_item = $item['harga'] * $item['jumlah']; 
                    $total_sementara += $subtotal_item; 
                ?>
                    <div class="group flex justify-between items-start">
                        <div class="flex-1 pr-4">
                            <span class="font-bold text-gray-900 text-sm block leading-tight mb-1"><?= $item['nama'] ?></span>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-semibold text-gray-500">Rp <?= number_format($item['harga'], 0, ',', '.') ?></span>
                                <span class="text-xs text-gray-300">•</span>
                                <span class="text-xs font-bold text-blue-600">x<?= $item['jumlah'] ?></span>
                            </div>
                        </div>
                        
                        <div class="flex flex-col items-end gap-2">
                            <span class="text-gray-900 font-bold text-sm">Rp <?= number_format($subtotal_item, 0, ',', '.') ?></span>
                            <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="proses/kurang.php?id=<?= $index ?>" class="aksi-keranjang bg-gray-100 hover:bg-gray-200 text-gray-600 rounded p-1 transition-colors" title="Kurangi">
                                    <i data-lucide="minus" class="w-3.5 h-3.5"></i>
                                </a>
                                <a href="proses/hapus.php?id=<?= $index ?>" class="aksi-keranjang bg-red-50 hover:bg-red-100 text-red-500 rounded p-1 transition-colors" title="Hapus">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="h-px bg-gray-100 w-full last:hidden"></div>
                <?php endforeach; ?>
                </div>
            <?php else : ?>
                <div class="h-full flex flex-col items-center justify-center text-center px-4">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                        <i data-lucide="shopping-cart" class="w-8 h-8 text-gray-300"></i>
                    </div>
                    <p class="text-gray-900 font-bold mb-1">Belum ada pesanan</p>
                    <p class="text-gray-500 text-sm">Buka kategori menu lalu klik tambah.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="p-5 border-t border-gray-200 bg-white shrink-0">
            <?php $total_sementara = isset($total_sementara) ? $total_sementara : 0; ?>
            <div class="flex justify-between items-end mb-4">
                <span class="text-sm font-semibold text-gray-500">Total Harga</span>
                <span class="text-3xl font-black text-gray-900 leading-none tracking-tight">Rp <?= number_format($total_sementara, 0, ',', '.') ?></span>
            </div>

            <form action="struk.php" method="POST">
                <div class="relative mb-3">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <span class="text-gray-500 font-bold">Rp</span>
                    </div>
                    <input type="number" name="uang_bayar" required min="<?= $total_sementara ?>" <?= ($total_sementara == 0) ? 'disabled' : '' ?> class="w-full pl-12 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl font-bold text-lg focus:outline-none focus:border-blue-500 focus:bg-white transition-colors" placeholder="0">
                </div>
                
                <button type="submit" <?= ($total_sementara == 0) ? 'disabled' : '' ?> class="w-full bg-blue-600 disabled:bg-gray-300 disabled:cursor-not-allowed hover:bg-blue-700 text-white font-bold py-4 rounded-xl transition-all flex justify-center items-center gap-2 shadow-sm shadow-blue-600/20">
                    <i data-lucide="wallet" class="w-5 h-5"></i>
                    Bayar Sekarang
                </button>
            </form>
        </div>
    </aside>

    <!-- SCRIPT LOGIKA DROPDOWN & AJAX -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            
            const dropdownBtn = document.getElementById('dropdown-btn');
            const dropdownMenu = document.getElementById('dropdown-menu');
            const dropdownIcon = document.getElementById('dropdown-icon');
            const dropdownLabel = document.getElementById('dropdown-label');
            
            // Logika Buka Tutup Laci
            dropdownBtn.addEventListener('click', (e) => {
                e.stopPropagation(); 
                dropdownMenu.classList.toggle('hidden');
                dropdownMenu.classList.toggle('flex');
                dropdownIcon.classList.toggle('rotate-180');
            });

            // Tutup otomatis saat klik area lain
            document.addEventListener('click', (e) => {
                if (!dropdownBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
                    dropdownMenu.classList.add('hidden');
                    dropdownMenu.classList.remove('flex');
                    dropdownIcon.classList.remove('rotate-180');
                }
            });

            const filterBtns = document.querySelectorAll('.filter-btn');
            const menuCards = document.querySelectorAll('.menu-card');
            const welcomeState = document.getElementById('welcome-state');
            const menuContainer = document.getElementById('menu-container');

            function aktifkanTab(target, labelText) {
                if (target === 'awal') {
                    dropdownLabel.textContent = 'Pilih Kategori...';
                    dropdownLabel.classList.remove('text-blue-700');
                    dropdownLabel.classList.add('text-gray-700');
                } else {
                    dropdownLabel.textContent = labelText;
                    dropdownLabel.classList.remove('text-gray-700');
                    dropdownLabel.classList.add('text-blue-700'); 
                }

                filterBtns.forEach(b => {
                    if (b.getAttribute('data-target') === target) {
                        b.classList.add('bg-blue-50', 'text-blue-700', 'font-bold');
                        b.classList.remove('text-gray-600', 'font-medium');
                    } else {
                        b.classList.remove('bg-blue-50', 'text-blue-700', 'font-bold');
                        b.classList.add('text-gray-600', 'font-medium');
                    }
                });

                if (target === 'awal') {
                    welcomeState.style.display = 'flex';
                    menuContainer.style.display = 'none';
                } else {
                    welcomeState.style.display = 'none';
                    menuContainer.style.display = 'grid';
                    
                    menuCards.forEach(card => {
                        const kategoriCard = card.getAttribute('data-kategori');
                        if (target === 'semua' || kategoriCard === target) {
                            card.style.display = 'flex';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                }

                dropdownMenu.classList.add('hidden');
                dropdownMenu.classList.remove('flex');
                dropdownIcon.classList.remove('rotate-180');

                sessionStorage.setItem('tabWarkopAktif', target);
                sessionStorage.setItem('tabWarkopLabel', labelText || 'Pilih Kategori...');
            }

            filterBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    aktifkanTab(btn.getAttribute('data-target'), btn.getAttribute('data-label'));
                });
            });

            aktifkanTab(
                sessionStorage.getItem('tabWarkopAktif') || 'awal', 
                sessionStorage.getItem('tabWarkopLabel') || 'Pilih Kategori...'
            );
            
            lucide.createIcons();

            const formTambah = document.querySelectorAll('.form-tambah');
            formTambah.forEach(form => {
                form.addEventListener('submit', async (e) => {
                    e.preventDefault(); 
                    const formData = new FormData(form);
                    const respons = await fetch('proses/tambah.php', { method: 'POST', body: formData });
                    const htmlBaru = await respons.text(); 
                    updateKeranjangUI(htmlBaru);
                });
            });

            function updateKeranjangUI(htmlTeks) {
                const parser = new DOMParser();
                const doc = parser.parseFromString(htmlTeks, 'text/html');
                document.getElementById('area-keranjang').innerHTML = doc.getElementById('area-keranjang').innerHTML;
                lucide.createIcons();
                pasangTombolAksi();
            }

            function pasangTombolAksi() {
                const tombolAksi = document.querySelectorAll('.aksi-keranjang');
                tombolAksi.forEach(tombol => {
                    tombol.addEventListener('click', async (e) => {
                        e.preventDefault(); 
                        const url = tombol.getAttribute('href');
                        const respons = await fetch(url);
                        const htmlBaru = await respons.text();
                        updateKeranjangUI(htmlBaru);
                    });
                });
            }

            pasangTombolAksi();
        });
    </script>
</body>
</html>