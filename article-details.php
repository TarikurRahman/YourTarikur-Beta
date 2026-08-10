<?php
require_once __DIR__ . '/includes/functions.php';

$slug = trim($_GET['slug'] ?? '');
$article = get_article_by_slug($slug);

if (!$article) {
    header("Location: index.php#articles");
    exit;
}

// Increment article view count safely
increment_article_views($article['id']);

require_once __DIR__ . '/includes/header.php';
?>

<main class="py-16 sm:py-24 bg-slate-950 light:bg-slate-50 min-h-[70vh]">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Link -->
        <a href="index.php#articles" class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-400 hover:underline mb-8">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Articles
        </a>

        <!-- Article Meta & Title -->
        <div class="space-y-4 mb-8">
            <div class="flex items-center gap-4 text-xs font-mono text-slate-400">
                <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    <?= e($article['read_time']) ?>
                </span>
                <span>Published <?= e($article['published_at']) ?></span>
                <span>• <?= e($article['views'] + 1) ?> Views</span>
            </div>
            <h1 class="text-3xl sm:text-5xl font-extrabold text-white light:text-slate-900 tracking-tight leading-tight">
                <?= e($article['title']) ?>
            </h1>
        </div>

        <!-- Featured Thumbnail Cover -->
        <div class="rounded-3xl overflow-hidden bg-slate-900 border border-slate-800 shadow-2xl mb-12 aspect-video">
            <img src="<?= e($article['thumbnail']) ?>" alt="<?= e($article['title']) ?>" class="w-full h-full object-cover">
        </div>

        <!-- Article Content -->
        <article class="prose prose-invert max-w-none text-slate-300 light:text-slate-700 leading-relaxed text-base sm:text-lg space-y-6">
            <p class="text-xl font-medium text-slate-200 light:text-slate-800 italic border-l-4 border-emerald-500 pl-4 py-1">
                <?= e($article['excerpt']) ?>
            </p>
            <div class="whitespace-pre-line">
                <?= nl2br(e($article['content'])) ?>
            </div>
        </article>

        <!-- Author Footer Card -->
        <div class="mt-16 pt-8 border-t border-slate-800 light:border-slate-200 flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-xl">
                TR
            </div>
            <div>
                <h4 class="font-bold text-base text-white light:text-slate-900">Written by <?= e($hero['name']) ?></h4>
                <p class="text-xs text-slate-400 light:text-slate-600"><?= e($hero['title']) ?></p>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
