<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/functions.php';

$hero = get_hero_info();
$site_settings = get_site_settings();
$site_title = !empty($site_settings['site_title']) ? e($site_settings['site_title']) : e($hero['name']) . " | " . e($hero['title']);
?>
<!DOCTYPE html>
<html lang="en" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $site_title ?></title>
    <meta name="description" content="<?= e($hero['subtitle']) ?>">
    <meta name="keywords" content="Full Stack Developer, PHP Developer, MySQL, React, Web Development Bangladesh, Portfolio">
    <meta name="google-site-verification" content="iOVrJHJzyIGMDpDM8Wpec4EPFamrO6IA3tIwPch9gvI">

    <!-- Tailwind CSS (via CDN) -->
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
                            700: '#047857',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- FontAwesome 6.5.1 CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">
    
    <!-- Custom Styles -->
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- Inline Early Theme Script (Prevents FOUT/Flash) -->
    <script>
        (function() {
            const storedTheme = localStorage.getItem('theme');
            if (storedTheme === 'dark' || (!storedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 min-h-screen flex flex-col font-sans transition-colors duration-300 antialiased selection:bg-emerald-500 selection:text-white">

    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-40 backdrop-blur-xl bg-white/80 dark:bg-slate-950/80 border-b border-slate-200/80 dark:border-slate-800/80 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="index.php" class="flex items-center gap-2 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 p-0.5 shadow-lg shadow-emerald-500/20 group-hover:scale-105 transition-transform duration-300">
                    <div class="w-full h-full bg-white dark:bg-slate-950 rounded-[10px] flex items-center justify-center">
                        <i data-lucide="code" class="w-5 h-5 text-emerald-500 dark:text-emerald-400"></i>
                    </div>
                </div>
                <span class="font-heading text-xl font-bold tracking-tight text-slate-900 dark:text-white">
                    <?= e($site_settings['site_logo_text'] ?? 'Tarikur.dev') ?>
                </span>
            </a>

            <!-- Desktop Navigation Links -->
            <nav class="hidden md:flex items-center gap-8 text-sm font-medium">
                <a href="index.php#home" class="text-slate-600 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Home</a>
                <a href="index.php#projects" class="text-slate-600 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Projects</a>
                <a href="index.php#awards" class="text-slate-600 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Awards</a>
                <a href="index.php#services" class="text-slate-600 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Services</a>
                <a href="index.php#gallery" class="text-slate-600 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Life Beyond Code</a>
                <a href="index.php#building" class="text-slate-600 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Building</a>
                <a href="index.php#articles" class="text-slate-600 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Writing</a>
                <a href="index.php#contact" class="text-slate-600 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Contact</a>
            </nav>

            <!-- Actions: Theme Switcher -->
            <div class="flex items-center gap-4">
                <!-- Theme Toggle Button -->
                <button id="theme-toggle" type="button" aria-label="Toggle Theme" class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 focus:outline-none transition-all shadow-sm">
                    <i id="theme-toggle-dark-icon" data-lucide="moon" class="w-5 h-5 hidden"></i>
                    <i id="theme-toggle-light-icon" data-lucide="sun" class="w-5 h-5 hidden"></i>
                </button>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-btn" type="button" class="md:hidden p-2.5 rounded-xl bg-slate-100 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 text-slate-700 dark:text-slate-300">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation Menu Drawer -->
        <div id="mobile-menu" class="hidden md:hidden border-b border-slate-200 dark:border-slate-800 bg-white/95 dark:bg-slate-950/95 px-6 py-6 space-y-4">
            <a href="index.php#home" class="block text-base font-medium text-slate-700 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400">Home</a>
            <a href="index.php#projects" class="block text-base font-medium text-slate-700 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400">Projects</a>
            <a href="index.php#awards" class="block text-base font-medium text-slate-700 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400">Awards & Achievements</a>
            <a href="index.php#services" class="block text-base font-medium text-slate-700 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400">Services</a>
            <a href="index.php#gallery" class="block text-base font-medium text-slate-700 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400">Life Beyond Code</a>
            <a href="index.php#building" class="block text-base font-medium text-slate-700 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400">Building</a>
            <a href="index.php#articles" class="block text-base font-medium text-slate-700 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400">Writing</a>
            <a href="index.php#contact" class="block text-base font-medium text-slate-700 dark:text-slate-300 hover:text-emerald-400">Contact</a>
        </div>
    </header>
