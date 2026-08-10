<?php
if (!ob_get_level()) {
    ob_start();
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../includes/functions.php';
require_admin_login();

$current_page = basename($_SERVER['PHP_SELF']);
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Portfolio Manager</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        emerald: {
                            400: '#34d399',
                            500: '#10b981',
                            600: '#059669',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Fonts -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col antialiased selection:bg-emerald-500 selection:text-white">

    <!-- Top Admin Header Bar -->
    <header class="bg-slate-900 border-b border-slate-800 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="dashboard.php" class="flex items-center gap-2 font-heading text-lg font-bold text-white">
                    <span class="w-8 h-8 rounded-lg bg-emerald-500 text-slate-950 flex items-center justify-center font-extrabold text-sm">&lt;&gt;</span>
                    Admin Control Panel
                </a>
            </div>

            <div class="flex items-center gap-4 text-xs">
                <a href="../index.php" target="_blank" class="px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-slate-300 hover:text-emerald-400 flex items-center gap-1.5 font-medium transition-colors">
                    <i data-lucide="external-link" class="w-4 h-4"></i> View Public Site
                </a>
                <a href="logout.php" class="px-3.5 py-2 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 hover:bg-rose-500/20 font-semibold transition-colors flex items-center gap-1.5">
                    <i data-lucide="log-out" class="w-4 h-4"></i> Sign Out
                </a>
            </div>
        </div>
    </header>

    <!-- Sub-Navbar Navigation Tabs -->
    <nav class="bg-slate-900/60 border-b border-slate-800 overflow-x-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex space-x-1 py-2 text-xs font-medium">
            <a href="dashboard.php" class="px-4 py-2 rounded-lg flex items-center gap-2 transition-colors <?= $current_page === 'dashboard.php' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 font-semibold' : 'text-slate-400 hover:text-white' ?>">
                <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Overview
            </a>
            <a href="manage-hero.php" class="px-4 py-2 rounded-lg flex items-center gap-2 transition-colors <?= $current_page === 'manage-hero.php' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 font-semibold' : 'text-slate-400 hover:text-white' ?>">
                <i data-lucide="user" class="w-4 h-4"></i> Hero & Profile
            </a>
            <a href="manage-projects.php" class="px-4 py-2 rounded-lg flex items-center gap-2 transition-colors <?= $current_page === 'manage-projects.php' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 font-semibold' : 'text-slate-400 hover:text-white' ?>">
                <i data-lucide="briefcase" class="w-4 h-4"></i> Projects
            </a>
            <a href="manage-awards.php" class="px-4 py-2 rounded-lg flex items-center gap-2 transition-colors <?= $current_page === 'manage-awards.php' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 font-semibold' : 'text-slate-400 hover:text-white' ?>">
                <i data-lucide="trophy" class="w-4 h-4"></i> Awards
            </a>
            <a href="manage-articles.php" class="px-4 py-2 rounded-lg flex items-center gap-2 transition-colors <?= $current_page === 'manage-articles.php' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 font-semibold' : 'text-slate-400 hover:text-white' ?>">
                <i data-lucide="file-text" class="w-4 h-4"></i> Articles
            </a>
            <a href="manage-services.php" class="px-4 py-2 rounded-lg flex items-center gap-2 transition-colors <?= $current_page === 'manage-services.php' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 font-semibold' : 'text-slate-400 hover:text-white' ?>">
                <i data-lucide="layers" class="w-4 h-4"></i> Services & Stats
            </a>
            <a href="manage-gallery.php" class="px-4 py-2 rounded-lg flex items-center gap-2 transition-colors <?= $current_page === 'manage-gallery.php' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 font-semibold' : 'text-slate-400 hover:text-white' ?>">
                <i data-lucide="image" class="w-4 h-4"></i> Polaroid Gallery
            </a>
            <a href="manage-building.php" class="px-4 py-2 rounded-lg flex items-center gap-2 transition-colors <?= $current_page === 'manage-building.php' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 font-semibold' : 'text-slate-400 hover:text-white' ?>">
                <i data-lucide="terminal" class="w-4 h-4"></i> Building
            </a>
            <a href="manage-messages.php" class="px-4 py-2 rounded-lg flex items-center gap-2 transition-colors <?= $current_page === 'manage-messages.php' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 font-semibold' : 'text-slate-400 hover:text-white' ?>">
                <i data-lucide="mail" class="w-4 h-4"></i> Messages
            </a>
            <a href="settings.php" class="px-4 py-2 rounded-lg flex items-center gap-2 transition-colors <?= $current_page === 'settings.php' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 font-semibold' : 'text-slate-400 hover:text-white' ?>">
                <i data-lucide="settings" class="w-4 h-4"></i> Settings
            </a>
        </div>
    </nav>

    <!-- Main Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1 w-full">

        <!-- Global Toast Alert Display -->
        <?php if ($flash): ?>
            <div class="mb-6 p-4 rounded-xl text-sm font-medium flex items-center justify-between shadow-lg <?= $flash['type'] === 'success' ? 'bg-emerald-500/10 border border-emerald-500/30 text-emerald-400' : 'bg-rose-500/10 border border-rose-500/30 text-rose-400' ?>">
                <div class="flex items-center gap-2">
                    <i data-lucide="<?= $flash['type'] === 'success' ? 'check-circle' : 'alert-circle' ?>" class="w-5 h-5"></i>
                    <span><?= e($flash['message']) ?></span>
                </div>
            </div>
        <?php endif; ?>
