<?php
session_start();
if (!isset($_SESSION['status_login'])) {
    header("Location: login.php");
    exit;
}

require_once 'koneksi.php';

// Ambil semua transaksi
$query_riwayat = mysqli_query($koneksi, "SELECT * FROM transaksi ORDER BY id_transaksi DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WarkopOS - Riwayat Transaksi</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="bg-gray-100 text-gray-900 font-sans antialiased h-screen flex overflow-hidden">

    <!-- SIDEBAR KIRI -->
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
            <a href="index.php" class="flex items-center gap-3 px-3 py-3 text-gray-400 hover:text-white hover:bg-gray-800 rounded-xl font-medium transition-all group">
                <i data-lucide="monitor" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                Terminal Kasir
            </a>
            
            <p class="px-3 text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2 mt-6">Manajemen Data</p>
            <a href="dashboard.php" class="flex items-center gap-3 px-3 py-3 text-gray-400 hover:text-white hover:bg-gray-800 rounded-xl font-medium transition-all group">
                <i data-lucide="bar-chart-2" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                Backoffice
            </a>
            <a href="riwayat.php" class="flex items-center gap-3 px-3 py-3 bg-blue-600 text-white rounded-xl font-semibold transition-colors shadow-sm shadow-blue-600/20">
                <i data-lucide="history" class="w-5 h-5"></i>
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

    <!-- AREA KONTEN TENGAH -->
    <main class="flex-1 flex flex-col bg-[#f8fafc] relative z-10 overflow-hidden">
        
        <header class="h-16 bg-white border-b border-gray-200 flex items-center px-6 md:px-8 shrink-0 z-10 shadow-sm sticky top-0">
            <h2 class="text-lg font-bold text-gray-900 tracking-tight">Manajemen Transaksi</h2>
        </header>

        <div class="flex-1 overflow-y-auto p-6 md:p-8">
            <div class="max-w-6xl mx-auto w-full">
                
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Data Transaksi</h2>
                        <p class="text-gray-500 font-medium mt-1 text-sm">Klik pada baris transaksi di bawah untuk melihat rincian pesanan.</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-8">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/80 border-b border-gray-100 text-[11px] uppercase tracking-widest text-gray-500 font-bold">
                                    <th class="p-5 whitespace-nowrap">ID Transaksi</th>
                                    <th class="p-5 whitespace-nowrap">Tanggal & Waktu</th>
                                    <th class="p-5 whitespace-nowrap">Status</th>
                                    <th class="p-5 whitespace-nowrap text-right">Total Belanja</th>
                                    <th class="p-5 whitespace-nowrap text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-100">
                                
                                <?php if (mysqli_num_rows($query_riwayat) > 0) : ?>
                                    <?php while ($row = mysqli_fetch_assoc($query_riwayat)) : ?>
                                    
                                    <!-- BARIS UTAMA (Bisa diklik) -->
                                    <tr class="hover:bg-blue-50/40 transition-colors group cursor-pointer" onclick="toggleDetail('<?= $row['id_transaksi'] ?>')">
                                        <td class="p-5 whitespace-nowrap">
                                            <span class="font-bold text-gray-900">#TRX-<?= str_pad($row['id_transaksi'], 5, '0', STR_PAD_LEFT) ?></span>
                                        </td>
                                        <td class="p-5 whitespace-nowrap text-gray-600 font-medium">
                                            <div class="flex items-center gap-2">
                                                <i data-lucide="calendar-clock" class="w-4 h-4 text-gray-400"></i>
                                                <?= date('d M Y • H:i', strtotime($row['tanggal'])) ?>
                                            </div>
                                        </td>
                                        <td class="p-5 whitespace-nowrap">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-green-50 text-green-700 border border-green-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                                Berhasil
                                            </span>
                                        </td>
                                        <td class="p-5 whitespace-nowrap text-right">
                                            <span class="font-black text-gray-900 tracking-tight">Rp <?= number_format($row['total_belanja'], 0, ',', '.') ?></span>
                                        </td>
                                        <td class="p-5 whitespace-nowrap text-center">
                                            <div id="icon-<?= $row['id_transaksi'] ?>" class="inline-flex items-center justify-center p-2 rounded-lg bg-gray-100 text-gray-500 group-hover:bg-blue-100 group-hover:text-blue-600 transition-all duration-300">
                                                <i data-lucide="chevron-down" class="w-4 h-4"></i>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- BARIS DETAIL LACI -->
                                    <tr id="detail-<?= $row['id_transaksi'] ?>" class="hidden bg-slate-50/80">
                                        <td colspan="5" class="p-0 border-b-2 border-gray-100">
                                            <div class="p-6 md:px-10 border-l-4 border-blue-500">
                                                <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-4">Rincian Pesanan</h4>
                                                
                                                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                                                    <?php
                                                    $id_trx = $row['id_transaksi'];
                                                    $ada_detail = false;
                                                    $query_detail = null;

                                                    // TRY-CATCH: Pelindung Anti-Crash!
                                                    // Jika tabel detail_transaksi belum ada, sistem TIDAK AKAN ERROR, melainkan langsung menampilkan pesan kosong.
                                                    try {
                                                        $query_detail = mysqli_query($koneksi, "SELECT * FROM detail_transaksi WHERE id_transaksi = '$id_trx'");
                                                        if ($query_detail && mysqli_num_rows($query_detail) > 0) {
                                                            $ada_detail = true;
                                                        }
                                                    } catch (Exception $e) {
                                                        $ada_detail = false;
                                                    }
                                                    
                                                    if ($ada_detail) :
                                                    ?>
                                                        <table class="w-full text-left">
                                                            <thead class="bg-gray-50/50 text-[11px] text-gray-500 font-semibold border-b border-gray-100 uppercase tracking-wider">
                                                                <tr>
                                                                    <th class="py-3 px-5">Menu</th>
                                                                    <th class="py-3 px-5 text-center">Qty</th>
                                                                    <th class="py-3 px-5 text-right">Harga Satuan</th>
                                                                    <th class="py-3 px-5 text-right">Subtotal</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="divide-y divide-gray-50">
                                                                <?php while ($detail = mysqli_fetch_assoc($query_detail)) : 
                                                                    $harga = isset($detail['harga']) ? $detail['harga'] : 0;
                                                                    $jumlah = isset($detail['jumlah']) ? $detail['jumlah'] : 1;
                                                                    $subtotal_item = $harga * $jumlah;
                                                                ?>
                                                                <tr class="hover:bg-gray-50/50">
                                                                    <td class="py-3 px-5 font-bold text-gray-800"><?= isset($detail['nama_menu']) ? $detail['nama_menu'] : 'Item' ?></td>
                                                                    <td class="py-3 px-5 text-center font-medium text-gray-600"><?= $jumlah ?>x</td>
                                                                    <td class="py-3 px-5 text-right text-gray-500">Rp <?= number_format($harga, 0, ',', '.') ?></td>
                                                                    <td class="py-3 px-5 text-right font-bold text-gray-900">Rp <?= number_format($subtotal_item, 0, ',', '.') ?></td>
                                                                </tr>
                                                                <?php endwhile; ?>
                                                            </tbody>
                                                        </table>
                                                    <?php else: ?>
                                                        <div class="py-8 text-center flex flex-col items-center">
                                                            <div class="bg-gray-50 p-3 rounded-full mb-3">
                                                                <i data-lucide="file-question" class="w-6 h-6 text-gray-400"></i>
                                                            </div>
                                                            <p class="text-gray-900 font-semibold text-sm">Belum ada rincian yang tercatat.</p>
                                                            <p class="text-gray-500 text-xs mt-1">Selesaikan pembuatan tabel Database dan update file Struk untuk memunculkannya.</p>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5" class="p-16 text-center">
                                            <p class="text-gray-900 font-bold mb-1">Belum ada transaksi</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <script>
        // Pastikan ikon diload dulu
        lucide.createIcons();

        // LOGIKA BUKA LACI
        function toggleDetail(id) {
            const detailRow = document.getElementById('detail-' + id);
            const iconWrapper = document.getElementById('icon-' + id);
            
            if (detailRow.classList.contains('hidden')) {
                // BUKA LACI
                detailRow.classList.remove('hidden');
                iconWrapper.classList.add('rotate-180', 'bg-blue-100', 'text-blue-600');
                iconWrapper.classList.remove('bg-gray-100', 'text-gray-500');
            } else {
                // TUTUP LACI
                detailRow.classList.add('hidden');
                iconWrapper.classList.remove('rotate-180', 'bg-blue-100', 'text-blue-600');
                iconWrapper.classList.add('bg-gray-100', 'text-gray-500');
            }
        }
    </script>
</body>
</html>