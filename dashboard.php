<?php
session_start();
if (!isset($_SESSION['status_login'])) {
    header("Location: login.php");
    exit;
}

require_once 'koneksi.php';

// AMBIL TOTAL OMZET HARI INI
$query_omzet = mysqli_query($koneksi, "SELECT SUM(total_belanja) as omzet FROM transaksi WHERE DATE(tanggal) = CURDATE()");
$data_omzet = mysqli_fetch_assoc($query_omzet);
$omzet_hari_ini = $data_omzet['omzet'] ? $data_omzet['omzet'] : 0;

// AMBIL JUMLAH TRANSAKSI HARI INI
$query_trx = mysqli_query($koneksi, "SELECT COUNT(id_transaksi) as jml_trx FROM transaksi WHERE DATE(tanggal) = CURDATE()");
$data_trx = mysqli_fetch_assoc($query_trx);
$trx_hari_ini = $data_trx['jml_trx'];

// AMBIL DATA GRAFIK
$query_grafik = mysqli_query($koneksi, "
    SELECT DATE(tanggal) as tgl, SUM(total_belanja) as total 
    FROM transaksi 
    GROUP BY DATE(tanggal) 
    ORDER BY tgl ASC 
    LIMIT 7
");

$label_tanggal = [];
$data_pendapatan = [];

while ($row = mysqli_fetch_assoc($query_grafik)) {
    $label_tanggal[] = date('d M', strtotime($row['tgl']));
    $data_pendapatan[] = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WarkopOS - Backoffice Dashboard</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

    <!-- SIDEBAR NAVIGASI KIRI (KONSISTEN) -->
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
            
            <!-- Tombol Dashboard (Aktif: Biru) -->
            <a href="dashboard.php" class="flex items-center gap-3 px-3 py-3 bg-blue-600 text-white rounded-xl font-semibold transition-colors shadow-sm shadow-blue-600/20">
                <i data-lucide="bar-chart-2" class="w-5 h-5"></i>
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

    <!-- AREA KONTEN TENGAH -->
    <main class="flex-1 flex flex-col bg-[#f8fafc] relative z-10 overflow-hidden">
        
        <!-- Header Halaman -->
        <header class="h-16 bg-white border-b border-gray-200 flex items-center px-6 md:px-8 shrink-0 z-10 shadow-sm sticky top-0">
            <h2 class="text-lg font-bold text-gray-900 tracking-tight">Ringkasan Bisnis</h2>
        </header>

        <!-- Konten Laporan -->
        <div class="flex-1 overflow-y-auto p-6 md:p-8">
            <div class="max-w-6xl mx-auto w-full">
                
                <!-- KARTU METRIK STATISTIK -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 flex items-center gap-5 hover:border-blue-300 hover:shadow-md transition-all duration-300">
                        <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 text-blue-600">
                            <i data-lucide="wallet" class="w-8 h-8"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Pendapatan Hari Ini</p>
                            <h3 class="text-3xl font-black text-gray-900 tracking-tight">Rp <?= number_format($omzet_hari_ini, 0, ',', '.') ?></h3>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 flex items-center gap-5 hover:border-green-300 hover:shadow-md transition-all duration-300">
                        <div class="bg-green-50 p-4 rounded-xl border border-green-100 text-green-600">
                            <i data-lucide="receipt" class="w-8 h-8"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Total Transaksi</p>
                            <div class="flex items-baseline gap-2">
                                <h3 class="text-3xl font-black text-gray-900 tracking-tight"><?= $trx_hari_ini ?></h3>
                                <span class="text-gray-500 font-semibold text-sm">Struk tercetak</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- AREA GRAFIK ANALYTICS -->
                <div class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-gray-200">
                    <div class="mb-6 flex items-center justify-between border-b border-gray-100 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="bg-gray-50 p-2 rounded-lg border border-gray-200">
                                <i data-lucide="trending-up" class="w-5 h-5 text-gray-600"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Grafik Penjualan</h3>
                                <p class="text-xs text-gray-500 font-medium">Data 7 hari terakhir</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="relative h-[350px] w-full">
                        <canvas id="grafikOmzet"></canvas>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <script>
        lucide.createIcons();
        const labelGrafik = <?= json_encode($label_tanggal) ?>;
        const dataGrafik = <?= json_encode($data_pendapatan) ?>;
        const ctx = document.getElementById('grafikOmzet').getContext('2d');
        
        let gradientBlue = ctx.createLinearGradient(0, 0, 0, 400);
        gradientBlue.addColorStop(0, 'rgba(37, 99, 235, 1)'); 
        gradientBlue.addColorStop(1, 'rgba(59, 130, 246, 0.6)'); 

        new Chart(ctx, {
            type: 'bar', 
            data: {
                labels: labelGrafik,
                datasets: [{
                    label: 'Omzet Harian',
                    data: dataGrafik,
                    backgroundColor: gradientBlue, 
                    borderRadius: 6, 
                    borderSkipped: false,
                    barThickness: 45 
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }, 
                    tooltip: {
                        backgroundColor: '#1f2937',
                        padding: 12,
                        titleFont: { size: 13, family: 'Inter', weight: 'normal' },
                        bodyFont: { size: 15, weight: 'bold', family: 'Inter' },
                        displayColors: false,
                        callbacks: {
                            label: function(context) { return 'Rp ' + context.raw.toLocaleString('id-ID'); }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9', drawBorder: false, borderDash: [5, 5] },
                        ticks: {
                            font: { family: 'Inter', size: 12, weight: '500' },
                            color: '#94a3b8',
                            padding: 10,
                            callback: function(value) { return value >= 1000 ? 'Rp ' + (value/1000) + 'k' : 'Rp ' + value; }
                        }
                    },
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: { font: { family: 'Inter', size: 12, weight: '600' }, color: '#64748b', padding: 10 }
                    }
                }
            }
        });
    </script>
</body>
</html>