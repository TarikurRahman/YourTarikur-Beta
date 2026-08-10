<?php
require_once __DIR__ . '/includes/header.php';

// Fetch dynamic content from database
$stats = get_stats();
$projects = get_projects();
$services = get_services();
$gallery = get_life_gallery();
$building = get_currently_building();
$articles = get_articles();
$awards = get_awards();

// Fetch GitHub real contribution data (with 1-hour cache)
$github_data = get_github_contributions($hero['github_username'] ?? 'TarikurRahman', $hero['github_token'] ?? '');
?>

<!-- 1. HERO SECTION -->
<section id="home" class="relative pt-12 sm:pt-20 pb-20 md:pb-28 overflow-hidden bg-slate-50 dark:bg-slate-950 transition-colors duration-300">
    <!-- Ambient Backdrop Lighting -->
    <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-emerald-500/10 rounded-full blur-[140px] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
            
            <!-- Left Hero Text & Call to Action -->
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                <!-- Profile Status Pill Badge -->
                <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-emerald-50 dark:bg-slate-900/90 border border-emerald-200 dark:border-emerald-500/30 shadow-sm">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </span>
                    <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-400 tracking-wide uppercase">
                        <?= e($hero['status_text']) ?>
                    </span>
                </div>

                <!-- Headline -->
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-[1.1]">
                    Hello, I'm <span class="text-gradient"><?= e($hero['name']) ?></span>
                </h1>
                <p class="text-xl sm:text-2xl font-semibold text-slate-700 dark:text-slate-300">
                    <?= e($hero['title']) ?>
                </p>

                <!-- Pitch Paragraph -->
                <p class="text-base sm:text-lg text-slate-600 dark:text-slate-400 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                    <?= e($hero['pitch']) ?>
                </p>

                <!-- Hero Action Buttons -->
                <div class="flex flex-wrap items-center justify-center lg:justify-start gap-4 pt-2">
                    <a href="#projects" class="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl text-base font-bold bg-emerald-500 hover:bg-emerald-400 text-slate-950 shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:-translate-y-0.5 transition-all">
                        <i data-lucide="briefcase" class="w-5 h-5"></i> See My Work
                    </a>
                    <a href="#contact" class="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl text-base font-bold bg-white dark:bg-slate-900 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-900 dark:text-white border border-slate-300 dark:border-slate-800 hover:border-emerald-500/40 hover:-translate-y-0.5 transition-all shadow-sm">
                        <i data-lucide="mail" class="w-5 h-5"></i> Hire Me
                    </a>
                    <?php if (!empty($hero['cv_url']) && $hero['cv_url'] !== '#'): ?>
                    <a href="<?= e($hero['cv_url']) ?>" target="_blank" class="inline-flex items-center gap-2 px-5 py-3.5 rounded-xl text-sm font-semibold text-slate-600 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                        <i data-lucide="download" class="w-4 h-4"></i> Download CV
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Hero Portrait Image -->
            <div class="lg:col-span-5 flex justify-center">
                <div class="relative w-72 h-72 sm:w-96 sm:h-96 group">
                    <div class="absolute -inset-1 rounded-3xl bg-gradient-to-r from-emerald-500 via-teal-400 to-emerald-600 opacity-60 blur-xl group-hover:opacity-100 transition duration-1000 group-hover:duration-200"></div>
                    
                    <div class="relative w-full h-full rounded-3xl bg-white dark:bg-slate-900 overflow-hidden border border-slate-200 dark:border-slate-800 shadow-2xl">
                        <img src="<?= e($hero['profile_image']) ?>" alt="<?= e($hero['name']) ?>" class="w-full h-full object-cover object-top group-hover:scale-105 transition duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 dark:from-slate-950 via-slate-950/10 to-transparent"></div>
                        
                        <div class="absolute bottom-4 left-4 right-4 p-3 rounded-2xl bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs font-mono shadow-md">
                            <span class="text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-emerald-500 dark:text-emerald-400"></i> Bangladesh
                            </span>
                            <span class="text-emerald-600 dark:text-emerald-400 font-semibold">Native PHP & MySQL</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- 2. KEY METRICS / STATS COUNTER BAR -->
<section class="py-10 bg-slate-100/80 dark:bg-slate-900/60 border-y border-slate-200 dark:border-slate-800/80 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            <?php foreach ($stats as $stat): ?>
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800/80 shadow-sm hover:border-emerald-500/40 transition-all">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 mb-3">
                        <i data-lucide="<?= e($stat['icon'] ?? 'award') ?>" class="w-6 h-6"></i>
                    </div>
                    <div class="stat-number font-heading text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white" data-target="<?= e($stat['stat_value']) ?>">
                        <?= e($stat['stat_value']) ?>
                    </div>
                    <div class="mt-1 text-xs sm:text-sm font-medium text-slate-600 dark:text-slate-400 uppercase tracking-wider">
                        <?= e($stat['stat_label']) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 3. FEATURED PROJECTS SECTION -->
<section id="projects" class="py-20 bg-slate-50 dark:bg-slate-950 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Portfolio</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight mt-1">
                    Featured Projects
                </h2>
            </div>
            <p class="text-slate-600 dark:text-slate-400 text-sm max-w-md mt-2 md:mt-0">
                A showcase of production-ready web applications, SaaS dashboards, and backend architectures.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($projects as $proj): ?>
                <article class="group rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 overflow-hidden shadow-lg hover:border-emerald-500/40 hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="relative aspect-video overflow-hidden bg-slate-950">
                            <img src="<?= e($proj['image_url']) ?>" alt="<?= e($proj['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <div class="absolute inset-0 bg-slate-950/20 group-hover:bg-transparent transition-colors"></div>
                        </div>

                        <div class="p-6">
                            <div class="flex flex-wrap gap-2 mb-3">
                                <?php 
                                $tags = explode(',', $proj['tech_stack']);
                                foreach ($tags as $tag): 
                                ?>
                                    <span class="px-2.5 py-1 rounded-md text-[11px] font-mono font-medium bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                        <?= e(trim($tag)) ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>

                            <h3 class="text-xl font-bold text-slate-900 dark:text-white group-hover:text-emerald-500 dark:group-hover:text-emerald-400 transition-colors">
                                <?= e($proj['title']) ?>
                            </h3>
                            <p class="mt-2 text-sm text-slate-600 dark:text-slate-400 line-clamp-3 leading-relaxed">
                                <?= e($proj['description']) ?>
                            </p>
                        </div>
                    </div>

                    <div class="px-6 pb-6 pt-2 flex items-center justify-between border-t border-slate-100 dark:border-slate-800/80 text-sm">
                        <a href="project-details.php?slug=<?= e($proj['slug']) ?>" class="font-semibold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1">
                            Details <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
                        </a>
                        <div class="flex items-center gap-3">
                            <?php if ($proj['github_url'] !== '#'): ?>
                                <a href="<?= e($proj['github_url']) ?>" target="_blank" class="text-slate-400 hover:text-slate-900 dark:hover:text-white" title="GitHub Code">
                                    <i data-lucide="github" class="w-4 h-4"></i>
                                </a>
                            <?php endif; ?>
                            <?php if ($proj['live_url'] !== '#'): ?>
                                <a href="<?= e($proj['live_url']) ?>" target="_blank" class="text-slate-400 hover:text-emerald-500 dark:hover:text-emerald-400" title="Live Demo">
                                    <i data-lucide="external-link" class="w-4 h-4"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 3. AWARDS & ACHIEVEMENTS SECTION -->
<section id="awards" class="py-20 bg-slate-50 dark:bg-slate-950 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <span class="text-xs font-semibold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Recognition & Competitions</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight mt-1">
                Awards & Achievements
            </h2>
            <p class="text-slate-600 dark:text-slate-400 text-sm mt-3">
                Honors, gold medals, and national science fair awards in IoT, Robotics, and Software Engineering.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <?php foreach ($awards as $award): ?>
                <div class="glow-card p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl flex flex-col justify-between relative overflow-hidden group">
                    <!-- Ambient Badge Lighting -->
                    <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/10 rounded-bl-full blur-2xl group-hover:bg-emerald-500/20 transition-all duration-300 pointer-events-none"></div>

                    <div>
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                                <i data-lucide="trophy" class="w-3.5 h-3.5"></i> <?= e($award['category']) ?>
                            </span>
                            <span class="text-xs font-mono text-slate-500 dark:text-slate-400 flex items-center gap-1">
                                <i data-lucide="calendar" class="w-3.5 h-3.5"></i> <?= e($award['event_date']) ?>
                            </span>
                        </div>

                        <h3 class="text-xl font-bold text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors leading-snug">
                            <?= e($award['title']) ?>
                        </h3>

                        <p class="mt-3 text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                            <?= e($award['description']) ?>
                        </p>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3 text-xs text-slate-500 dark:text-slate-400">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 px-2.5 py-0.5 rounded-md border border-emerald-500/20">
                                <?= e($award['team_name']) ?>
                            </span>
                            <span><?= e($award['institution']) ?></span>
                        </div>
                        <div class="flex items-center gap-1.5 font-medium text-slate-600 dark:text-slate-400">
                            <i data-lucide="map-pin" class="w-3.5 h-3.5 text-emerald-500"></i> <?= e($award['location']) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 4. SERVICES SECTION -->
<section id="services" class="py-20 bg-slate-100/60 dark:bg-slate-900/40 border-y border-slate-200 dark:border-slate-800/80 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <span class="text-xs font-semibold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Capabilities</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight mt-1">
                What I Can Help You With
            </h2>
            <p class="text-slate-600 dark:text-slate-400 text-sm mt-3">
                Tailored web engineering solutions designed for business growth, reliability, and speed.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($services as $svc): ?>
                <div class="glow-card p-8 rounded-2xl bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 shadow-md flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-6">
                            <i data-lucide="<?= e($svc['icon'] ?? 'code-2') ?>" class="w-6 h-6"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                            <?= e($svc['title']) ?>
                        </h3>
                        <p class="mt-3 text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                            <?= e($svc['description']) ?>
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800/80 flex items-center text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                        <span>Learn More</span> <i data-lucide="chevron-right" class="w-4 h-4 ml-1"></i>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 5. LIFE BEYOND CODE (Photo Gallery Section) -->
<section id="gallery" class="py-20 bg-slate-50 dark:bg-slate-950 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <span class="text-xs font-semibold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Personal Side</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight mt-1">
                Life Beyond Code
            </h2>
            <p class="text-slate-600 dark:text-slate-400 text-sm mt-3">
                When I'm not writing code, I love traveling, exploring nature, and speaking at local tech meetups.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php foreach ($gallery as $item): ?>
                <div class="polaroid-card bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xl flex flex-col justify-between">
                    <div class="relative aspect-[4/3] overflow-hidden rounded-xl bg-slate-950 mb-4">
                        <img src="<?= e($item['image_url']) ?>" alt="<?= e($item['title']) ?>" class="w-full h-full object-cover">
                        <?php if (!empty($item['location'])): ?>
                            <span class="absolute bottom-2 left-2 px-2.5 py-1 rounded-md text-[10px] font-semibold bg-slate-950/80 backdrop-blur-md text-emerald-400 border border-slate-800 flex items-center gap-1">
                                <i data-lucide="map-pin" class="w-3 h-3"></i> <?= e($item['location']) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h4 class="font-bold text-base text-slate-900 dark:text-white">
                            <?= e($item['title']) ?>
                        </h4>
                        <p class="mt-1 text-xs text-slate-600 dark:text-slate-400 line-clamp-2">
                            <?= e($item['caption']) ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 6. GITHUB ACTIVITY SECTION -->
<section class="py-16 bg-slate-100/80 dark:bg-slate-900/60 border-y border-slate-200 dark:border-slate-800/80 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-slate-950 p-8 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xl">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 flex items-center justify-center">
                        <i data-lucide="github" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            Open Source & GitHub Activity 
                            <a href="https://github.com/<?= e($github_data['username']) ?>" target="_blank" class="text-xs font-mono text-emerald-600 dark:text-emerald-400 hover:underline">@<?= e($github_data['username']) ?></a>
                        </h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400">Live contributions & commit metrics over the last 12 months</p>
                    </div>
                </div>
                <div class="flex items-center gap-4 text-xs font-mono">
                    <span class="text-slate-600 dark:text-slate-400">Total Contributions: <strong id="github-total-commits" class="text-emerald-600 dark:text-emerald-400"><?= number_format($github_data['total_contributions']) ?></strong></span>
                    <span class="text-slate-600 dark:text-slate-400">Current Streak: <strong class="text-emerald-600 dark:text-emerald-400"><?= e($github_data['current_streak']) ?> Days</strong></span>
                </div>
            </div>

            <!-- Heatmap Grid Container -->
            <div class="overflow-x-auto pb-2">
                <div id="github-heatmap-grid" class="flex gap-1 min-w-[700px] justify-between">
                    <?php foreach ($github_data['weeks'] as $week_idx => $days): ?>
                        <div class="flex flex-col gap-1">
                            <?php foreach ($days as $day): ?>
                                <div class="w-3 h-3 rounded-sm contrib-level-<?= e($day['level']) ?> transition-all duration-200 hover:scale-125 cursor-pointer relative group"
                                     title="<?= e($day['count']) ?> contribution<?= $day['count'] == 1 ? '' : 's' ?> on <?= e($day['date']) ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Heatmap Legend -->
            <div class="flex items-center justify-between text-xs text-slate-600 dark:text-slate-400 mt-4 pt-4 border-t border-slate-100 dark:border-slate-900">
                <a href="https://github.com/<?= e($github_data['username']) ?>" target="_blank" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors flex items-center gap-1 font-medium">
                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i> View @<?= e($github_data['username']) ?> on GitHub
                </a>
                <div class="flex items-center gap-1.5">
                    <span>Less</span>
                    <span class="w-3 h-3 rounded-sm contrib-level-0" title="No contributions"></span>
                    <span class="w-3 h-3 rounded-sm contrib-level-1" title="1-2 contributions"></span>
                    <span class="w-3 h-3 rounded-sm contrib-level-2" title="3-4 contributions"></span>
                    <span class="w-3 h-3 rounded-sm contrib-level-3" title="5-6 contributions"></span>
                    <span class="w-3 h-3 rounded-sm contrib-level-4" title="7+ contributions"></span>
                    <span>More</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 7. CURRENTLY EXPLORING & BUILDING SECTION -->
<section id="building" class="py-20 bg-slate-50 dark:bg-slate-950 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <span class="text-xs font-semibold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Active R&D</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight mt-1">
                Currently Exploring & Building
            </h2>
            <p class="text-slate-600 dark:text-slate-400 text-sm mt-3">
                Staying at the bleeding edge of modern web technology, distributed systems, and AI workflows.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach ($building as $item): ?>
                <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-md flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                <?= e($item['status']) ?>
                            </span>
                            <i data-lucide="<?= e($item['icon'] ?? 'terminal') ?>" class="w-5 h-5 text-slate-400"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                            <?= e($item['title']) ?>
                        </h3>
                        <p class="mt-2 text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                            <?= e($item['description']) ?>
                        </p>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800/80">
                        <div class="flex justify-between text-xs font-semibold mb-1">
                            <span class="text-slate-600 dark:text-slate-400">Completion</span>
                            <span class="text-emerald-600 dark:text-emerald-400"><?= e($item['progress_percent']) ?>%</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-slate-200 dark:bg-slate-950 overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-400 rounded-full" style="width: <?= e($item['progress_percent']) ?>%"></div>
                        </div>
                        <?php if (!empty($item['tech_stack'])): ?>
                            <div class="mt-3 text-[11px] font-mono text-slate-500">
                                Tech: <?= e($item['tech_stack']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 8. LATEST WRITING (Blog/Articles Section) -->
<section id="articles" class="py-20 bg-slate-100/60 dark:bg-slate-900/40 border-t border-slate-200 dark:border-slate-800/80 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Blog & Insights</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight mt-1">
                    Latest Writing
                </h2>
            </div>
            <p class="text-slate-600 dark:text-slate-400 text-sm max-w-md mt-2 md:mt-0">
                Articles on Native PHP, backend security, performance tuning, and frontend engineering.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <?php foreach ($articles as $art): ?>
                <article class="p-6 rounded-3xl bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 shadow-md hover:border-emerald-500/40 transition-all flex flex-col md:flex-row gap-6">
                    <div class="md:w-2/5 aspect-video md:aspect-auto rounded-2xl overflow-hidden bg-slate-900 shrink-0">
                        <img src="<?= e($art['thumbnail']) ?>" alt="<?= e($art['title']) ?>" class="w-full h-full object-cover">
                    </div>
                    <div class="md:w-3/5 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-3 text-xs text-slate-500 dark:text-slate-400 mb-2 font-mono">
                                <span><i data-lucide="calendar" class="w-3.5 h-3.5 inline mr-1 text-emerald-500 dark:text-emerald-400"></i><?= e($art['published_at']) ?></span>
                                <span>•</span>
                                <span><?= e($art['read_time']) ?></span>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white hover:text-emerald-500 dark:hover:text-emerald-400 transition-colors">
                                <a href="article-details.php?slug=<?= e($art['slug']) ?>">
                                    <?= e($art['title']) ?>
                                </a>
                            </h3>
                            <p class="mt-2 text-xs sm:text-sm text-slate-600 dark:text-slate-400 line-clamp-2 leading-relaxed">
                                <?= e($art['excerpt']) ?>
                            </p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-xs">
                            <a href="article-details.php?slug=<?= e($art['slug']) ?>" class="font-semibold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1">
                                Read Article <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                            </a>
                            <span class="text-slate-500 font-mono"><i data-lucide="eye" class="w-3.5 h-3.5 inline mr-1"></i><?= e($art['views']) ?> views</span>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 9. CONTACT FORM SECTION -->
<section id="contact" class="py-20 bg-slate-50 dark:bg-slate-950 transition-colors duration-300">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="text-xs font-semibold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Get In Touch</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight mt-1">
                Send Me A Message
            </h2>
            <p class="text-slate-600 dark:text-slate-400 text-sm mt-2">
                Have a question or want to discuss a project proposal? Fill out the form below.
            </p>
        </div>

        <div class="bg-white dark:bg-slate-900 p-8 sm:p-10 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl">
            <div id="contact-alert" class="hidden"></div>

            <form id="portfolio-contact-form" class="space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="sender_name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Your Name</label>
                        <input type="text" id="sender_name" name="sender_name" required placeholder="John Doe" class="w-full px-4 py-3.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors">
                    </div>
                    <div>
                        <label for="sender_email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Your Email</label>
                        <input type="email" id="sender_email" name="sender_email" required placeholder="john@example.com" class="w-full px-4 py-3.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors">
                    </div>
                </div>

                <div>
                    <label for="subject" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Subject</label>
                    <input type="text" id="subject" name="subject" required placeholder="Project Inquiry / Hiring Proposal" class="w-full px-4 py-3.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors">
                </div>

                <div>
                    <label for="message" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Message</label>
                    <textarea id="message" name="message" rows="5" required placeholder="Tell me about your project, timeline, and requirements..." class="w-full px-4 py-3.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors"></textarea>
                </div>

                <button type="submit" class="w-full py-4 rounded-xl text-base font-bold bg-emerald-500 hover:bg-emerald-400 text-slate-950 shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 transition-all flex items-center justify-center gap-2">
                    <i data-lucide="send" class="w-5 h-5"></i> Send Message
                </button>
            </form>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
