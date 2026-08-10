<?php
require_once __DIR__ . '/includes/functions.php';

$slug = trim($_GET['slug'] ?? '');
$project = get_project_by_slug($slug);

if (!$project) {
    header("Location: index.php#projects");
    exit;
}

require_once __DIR__ . '/includes/header.php';
?>

<main class="py-16 sm:py-24 bg-slate-950 light:bg-slate-50 min-h-[70vh]">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Navigation -->
        <a href="index.php#projects" class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-400 hover:underline mb-8">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Projects
        </a>

        <!-- Project Title Header -->
        <div class="space-y-4 mb-8">
            <div class="flex flex-wrap gap-2">
                <?php 
                $tags = explode(',', $project['tech_stack']);
                foreach ($tags as $tag): 
                ?>
                    <span class="px-3 py-1 rounded-md text-xs font-mono font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                        <?= e(trim($tag)) ?>
                    </span>
                <?php endforeach; ?>
            </div>
            <h1 class="text-3xl sm:text-5xl font-extrabold text-white light:text-slate-900 tracking-tight">
                <?= e($project['title']) ?>
            </h1>
            <p class="text-lg text-slate-400 light:text-slate-600">
                <?= e($project['description']) ?>
            </p>
        </div>

        <!-- Project Hero Cover -->
        <div class="rounded-3xl overflow-hidden bg-slate-900 border border-slate-800 shadow-2xl mb-12">
            <img src="<?= e($project['image_url']) ?>" alt="<?= e($project['title']) ?>" class="w-full h-auto max-h-[500px] object-cover">
        </div>

        <!-- Details & Content -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            <div class="md:col-span-2 space-y-6 text-slate-300 light:text-slate-700 leading-relaxed">
                <h2 class="text-2xl font-bold text-white light:text-slate-900">Project Overview</h2>
                <div class="whitespace-pre-line font-body text-base">
                    <?= nl2br(e($project['full_description'] ?? $project['description'])) ?>
                </div>
            </div>

            <!-- Sidebar Actions -->
            <div class="space-y-6">
                <div class="p-6 rounded-2xl bg-slate-900 light:bg-white border border-slate-800 light:border-slate-200 shadow-md space-y-4">
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-400">Links & Actions</h3>
                    <?php if (!empty($project['live_url']) && $project['live_url'] !== '#'): ?>
                        <a href="<?= e($project['live_url']) ?>" target="_blank" class="w-full py-3 px-4 rounded-xl text-sm font-bold bg-emerald-500 hover:bg-emerald-400 text-slate-950 flex items-center justify-center gap-2 shadow-lg shadow-emerald-500/20">
                            <i data-lucide="external-link" class="w-4 h-4"></i> Live Project Demo
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($project['github_url']) && $project['github_url'] !== '#'): ?>
                        <a href="<?= e($project['github_url']) ?>" target="_blank" class="w-full py-3 px-4 rounded-xl text-sm font-semibold bg-slate-950 light:bg-slate-100 hover:bg-slate-800 text-slate-200 light:text-slate-800 border border-slate-800 light:border-slate-300 flex items-center justify-center gap-2">
                            <i data-lucide="github" class="w-4 h-4"></i> Source Repository
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
