<?php
session_start();
if (isset($_SESSION['status_login'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autentikasi - POS System</title>
    
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
                    colors: { brand: '#2563eb' } // blue-600
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased min-h-screen flex items-center justify-center p-4">
    
    <div class="bg-white w-full max-w-[400px] p-8 rounded-xl shadow-sm border border-gray-200">
        
        <div class="flex flex-col items-center text-center mb-8">
            <div class="bg-blue-50 p-3 rounded-xl mb-4 border border-blue-100">
                <i data-lucide="lock" class="w-6 h-6 text-blue-600"></i>
            </div>
            <h2 class="text-2xl font-semibold tracking-tight text-gray-900 mb-1">Autentikasi</h2>
            <p class="text-sm text-gray-500 font-medium">Masuk untuk mengakses sistem</p>
        </div>

        <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'gagal') : ?>
            <div class="bg-gray-900 text-white px-4 py-3 rounded-lg mb-6 text-sm flex items-center gap-3">
                <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
                <span class="font-medium">Kredensial tidak valid.</span>
            </div>
        <?php endif; ?>

        <form action="proses/auth.php" method="POST" class="flex flex-col gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Username</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="user" class="w-4 h-4 text-gray-400"></i>
                    </div>
                    <input type="text" name="username" required 
                        class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors" 
                        placeholder="Masukkan username">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="key" class="w-4 h-4 text-gray-400"></i>
                    </div>
                    <input type="password" name="password" required 
                        class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors" 
                        placeholder="••••••••">
                </div>
            </div>
            
            <!-- Tombol Solid 1 Warna Tanpa Gradasi -->
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-lg transition-colors text-sm flex justify-center items-center gap-2 mt-2">
                Masuk Sistem
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </button>
        </form>
    </div>

    <script> lucide.createIcons(); </script>
</body>
</html>