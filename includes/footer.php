<?php
/**
 * Shared Footer with Mountain Vector Illustration & Dynamic Settings
 */
$site_settings = get_site_settings();
$github_link = !empty($site_settings['github_url']) ? $site_settings['github_url'] : 'https://github.com/tarikurrahman';
$linkedin_link = !empty($site_settings['linkedin_url']) ? $site_settings['linkedin_url'] : 'https://www.linkedin.com/in/tarikurrahman08';
$facebook_link = !empty($site_settings['facebook_url']) ? $site_settings['facebook_url'] : 'https://www.facebook.com/tarikurrahman08';
$instagram_link = !empty($site_settings['instagram_url']) ? $site_settings['instagram_url'] : 'https://www.instagram.com/tarikurrahman08';
$tiktok_link = !empty($site_settings['tiktok_url']) ? $site_settings['tiktok_url'] : 'https://www.tiktok.com/@tarikurrahman.bd';
$twitter_link = !empty($site_settings['twitter_url']) ? $site_settings['twitter_url'] : 'https://x.com/tarikurrahman08';
$email_link = !empty($site_settings['contact_email']) ? 'mailto:' . $site_settings['contact_email'] : 'mailto:tarikur@example.com';
?>
    <!-- Mountain Landscape Illustration Vector Graphic Container -->
    <div class="w-full bg-slate-100 dark:bg-slate-950 overflow-hidden leading-none -mb-1 pointer-events-none select-none transition-colors duration-300">
        <svg class="w-full h-24 sm:h-36 md:h-48 text-slate-200 dark:text-slate-900 fill-current transition-colors duration-300" viewBox="0 0 1440 320" preserveAspectRatio="none">
            <path d="M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,224C672,245,768,267,864,250.7C960,235,1056,181,1152,165.3C1248,149,1344,171,1392,181.3L1440,192L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
        </svg>
    </div>

    <!-- Main Footer -->
    <footer class="bg-slate-200 dark:bg-slate-900 border-t border-slate-300 dark:border-slate-800 text-slate-600 dark:text-slate-400 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <!-- Col 1: Bio & Socials -->
                <div class="space-y-4">
                    <a href="index.php" class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500 flex items-center justify-center text-slate-950 font-bold">
                            &lt;&gt;
                        </div>
                        <span class="font-heading text-lg font-bold text-slate-900 dark:text-white">
                            <?= e($hero['name']) ?>
                        </span>
                    </a>
                    <p class="text-sm text-slate-600 dark:text-slate-400 max-w-sm">
                        <?= e($hero['subtitle']) ?>
                    </p>
                    <div class="flex flex-wrap items-center gap-2 pt-2">
                        <a href="<?= e($github_link) ?>" target="_blank" rel="noopener" aria-label="GitHub Profile" title="GitHub" class="w-9 h-9 rounded-xl bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:border-emerald-500/40 transition-all shadow-sm">
                            <i class="fa-brands fa-github text-base"></i>
                        </a>
                        <a href="<?= e($linkedin_link) ?>" target="_blank" rel="noopener" aria-label="LinkedIn Profile" title="LinkedIn" class="w-9 h-9 rounded-xl bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:border-emerald-500/40 transition-all shadow-sm">
                            <i class="fa-brands fa-linkedin-in text-base"></i>
                        </a>
                        <a href="<?= e($facebook_link) ?>" target="_blank" rel="noopener" aria-label="Facebook Profile" title="Facebook" class="w-9 h-9 rounded-xl bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:border-emerald-500/40 transition-all shadow-sm">
                            <i class="fa-brands fa-facebook-f text-base"></i>
                        </a>
                        <a href="<?= e($instagram_link) ?>" target="_blank" rel="noopener" aria-label="Instagram Profile" title="Instagram" class="w-9 h-9 rounded-xl bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:border-emerald-500/40 transition-all shadow-sm">
                            <i class="fa-brands fa-instagram text-base"></i>
                        </a>
                        <a href="<?= e($tiktok_link) ?>" target="_blank" rel="noopener" aria-label="TikTok Profile" title="TikTok" class="w-9 h-9 rounded-xl bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:border-emerald-500/40 transition-all shadow-sm">
                            <i class="fa-brands fa-tiktok text-base"></i>
                        </a>
                        <a href="<?= e($twitter_link) ?>" target="_blank" rel="noopener" aria-label="Twitter X Profile" title="Twitter / X" class="w-9 h-9 rounded-xl bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:border-emerald-500/40 transition-all shadow-sm">
                            <i class="fa-brands fa-x-twitter text-base"></i>
                        </a>
                        <a href="<?= e($email_link) ?>" aria-label="Email Contact" title="Email Contact" class="w-9 h-9 rounded-xl bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:border-emerald-500/40 transition-all shadow-sm">
                            <i class="fa-solid fa-envelope text-sm"></i>
                        </a>
                    </div>
                </div>

                <!-- Col 2: Navigation Links -->
                <div>
                    <h3 class="font-heading text-sm font-semibold text-slate-900 dark:text-white uppercase tracking-wider mb-4">Navigation</h3>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="index.php#home" class="hover:text-emerald-500 dark:hover:text-emerald-400 transition-colors">Home</a></li>
                        <li><a href="index.php#projects" class="hover:text-emerald-500 dark:hover:text-emerald-400 transition-colors">Featured Projects</a></li>
                        <li><a href="index.php#awards" class="hover:text-emerald-500 dark:hover:text-emerald-400 transition-colors">Awards & Honors</a></li>
                        <li><a href="index.php#services" class="hover:text-emerald-500 dark:hover:text-emerald-400 transition-colors">Capabilities & Services</a></li>
                        <li><a href="index.php#gallery" class="hover:text-emerald-500 dark:hover:text-emerald-400 transition-colors">Life Beyond Code</a></li>
                        <li><a href="index.php#building" class="hover:text-emerald-500 dark:hover:text-emerald-400 transition-colors">Active R&D</a></li>
                        <li><a href="index.php#articles" class="hover:text-emerald-500 dark:hover:text-emerald-400 transition-colors">Technical Writing</a></li>
                        <li><a href="index.php#contact" class="hover:text-emerald-500 dark:hover:text-emerald-400 transition-colors">Contact Me</a></li>
                    </ul>
                </div>

                <!-- Col 3: Tech Focus & Availability -->
                <div>
                    <h3 class="font-heading text-sm font-semibold text-slate-900 dark:text-white uppercase tracking-wider mb-4">Engineering Focus</h3>
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed mb-3">
                        Specializing in modern web applications, scalable Native PHP architectures, high-performance PDO database queries, and reactive Tailwind interfaces.
                    </p>
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 text-xs font-medium">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Available for Remote Contracts
                    </div>
                </div>
            </div>

            <!-- Bottom Copyright Bar -->
            <div class="mt-12 pt-8 border-t border-slate-300 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 dark:text-slate-400 gap-4">
                <p>&copy; <?= date('Y') ?> <?= e($hero['name']) ?>. <?= e($site_settings['footer_copyright'] ?? 'All rights reserved.') ?></p>
                <p class="flex items-center gap-1">
                    Powered by <span class="text-emerald-600 dark:text-emerald-400 font-semibold">Antigravity Engine</span>
                </p>
            </div>
        </div>
    </footer>

    <!-- Lucide Icon Initialization -->
    <script>
        lucide.createIcons();
    </script>
    <script src="assets/js/main.js"></script>

<?php if ($site_settings['ai_chatbot_enabled'] ?? 1): ?>
    <!-- Floating AI Chatbot Widget Button -->
    <div id="ai-chatbot-widget" class="fixed bottom-4 right-4 sm:bottom-6 sm:right-6 z-50">
        <button id="ai-chat-toggle-btn" aria-label="Toggle AI Assistant" class="relative group flex items-center justify-center w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-400 p-0.5 shadow-2xl shadow-emerald-500/40 hover:scale-105 active:scale-95 transition-all duration-300">
            <div class="w-full h-full bg-slate-950 rounded-[14px] flex items-center justify-center relative overflow-hidden">
                <i data-lucide="bot" class="w-5 h-5 sm:w-6 sm:h-6 text-emerald-400 group-hover:rotate-12 transition-transform duration-300"></i>
                <span class="absolute top-2 right-2 w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                <span class="absolute top-2 right-2 w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
            </div>
        </button>

        <!-- Floating AI Chat Modal Container -->
        <div id="ai-chat-modal" class="fixed bottom-20 right-4 sm:bottom-24 sm:right-6 w-96 max-w-[calc(100vw-2rem)] h-[480px] sm:h-[520px] bg-slate-900/95 border border-slate-700/80 rounded-3xl shadow-2xl overflow-hidden backdrop-blur-2xl flex flex-col transition-all duration-300 hidden z-50 text-white">
            <!-- Modal Header -->
            <div class="p-4 bg-slate-950/80 border-b border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400">
                        <i data-lucide="bot" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-white flex items-center gap-1.5 font-heading">
                            Tarikur.dev AI Assistant
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-mono bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">ONLINE</span>
                        </h4>
                        <p class="text-[10px] text-slate-400">Ask about projects, awards, or hiring Tarikur</p>
                    </div>
                </div>
                <button id="ai-chat-close-btn" class="p-2 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-colors min-w-[40px] min-h-[40px] flex items-center justify-center">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <!-- Chat Message History Window -->
            <div id="ai-chat-messages" class="flex-1 p-4 overflow-y-auto space-y-3.5 text-xs">
                <!-- Welcome Message -->
                <div class="flex items-start gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 shrink-0">
                        <i data-lucide="bot" class="w-3.5 h-3.5"></i>
                    </div>
                    <div class="bg-slate-800/90 border border-slate-700/60 rounded-2xl rounded-tl-none p-3 max-w-[85%] text-slate-200 leading-relaxed shadow-sm">
                        Hello! 👋 I am Tarikur's AI Assistant. How can I help you today?
                    </div>
                </div>
            </div>

            <!-- Quick Suggestion Pills -->
            <div id="ai-chat-pills" class="px-3 py-2 bg-slate-950/40 border-t border-slate-800/80 flex items-center gap-2 overflow-x-auto no-scrollbar text-[11px]">
                <button class="ai-pill whitespace-nowrap px-3 py-1.5 rounded-full bg-slate-800 hover:bg-slate-700 text-emerald-300 border border-slate-700/60 transition-colors shrink-0" data-prompt="Tell me about Tarikur's Gold Medal win at WICE 2026">
                    ⚡ WICE 2026 Gold Medal
                </button>
                <button class="ai-pill whitespace-nowrap px-3 py-1.5 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700/60 transition-colors shrink-0" data-prompt="What are Tarikur's top projects?">
                    💻 Top Projects
                </button>
                <button class="ai-pill whitespace-nowrap px-3 py-1.5 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700/60 transition-colors shrink-0" data-prompt="How can I hire Tarikur for a contract?">
                    🤝 How to Hire
                </button>
            </div>

            <!-- Chat Input Area -->
            <form id="ai-chat-form" class="p-3 bg-slate-950 border-t border-slate-800 flex items-center gap-2">
                <input type="text" id="ai-chat-input" placeholder="Type a message or question..." autocomplete="off" class="flex-1 px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white placeholder-slate-500 text-xs focus:outline-none focus:border-emerald-500">
                <button type="submit" id="ai-chat-send-btn" class="w-10 h-10 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 flex items-center justify-center font-bold transition-all shadow-md shadow-emerald-500/20 shrink-0">
                    <i data-lucide="send" class="w-4 h-4"></i>
                </button>
            </form>
        </div>
    </div>
<?php endif; ?>
</body>
</html>
