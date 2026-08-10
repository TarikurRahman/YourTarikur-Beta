<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin_login();

$db = getDB();
$hero = get_hero_info();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    
    if (!verify_csrf_token($token)) {
        set_flash('error', 'Invalid CSRF token.');
        redirect('manage-hero.php');
    }

    $status_text = trim($_POST['status_text'] ?? 'Available for work');
    $name = trim($_POST['name'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $subtitle = trim($_POST['subtitle'] ?? '');
    $pitch = trim($_POST['pitch'] ?? '');
    $cv_url = trim($_POST['cv_url'] ?? '#');
    $github_username = trim($_POST['github_username'] ?? 'TarikurRahman');
    $github_token = trim($_POST['github_token'] ?? '');
    $profile_image = $hero['profile_image'];

    // Handle File Upload if provided
    try {
        $uploaded = upload_image('profile_image_file', 'uploads');
        if ($uploaded) {
            $profile_image = '../' . $uploaded;
        } elseif (!empty($_POST['profile_image_url'])) {
            $profile_image = trim($_POST['profile_image_url']);
        }
    } catch (Exception $ex) {
        set_flash('error', $ex->getMessage());
        redirect('manage-hero.php');
    }

    try {
        $stmt = $db->prepare("UPDATE hero_info SET 
            status_text = :status_text,
            name = :name,
            title = :title,
            subtitle = :subtitle,
            pitch = :pitch,
            cv_url = :cv_url,
            profile_image = :profile_image,
            github_username = :github_username,
            github_token = :github_token
            WHERE id = :id");

        $stmt->execute([
            ':status_text' => $status_text,
            ':name' => $name,
            ':title' => $title,
            ':subtitle' => $subtitle,
            ':pitch' => $pitch,
            ':cv_url' => $cv_url,
            ':profile_image' => $profile_image,
            ':github_username' => $github_username,
            ':github_token' => $github_token,
            ':id' => $hero['id']
        ]);

        // Purge old GitHub cache files to reflect immediate updates
        array_map('unlink', glob(__DIR__ . '/../data/github_cache_*.json'));

        set_flash('success', 'Hero profile & GitHub settings updated successfully!');
    } catch (Exception $e) {
        set_flash('error', 'Database error: ' . $e->getMessage());
    }
    redirect('manage-hero.php');
}

// Render HTML layout AFTER handling actions
require_once __DIR__ . '/admin-header.php';
$csrf_token = generate_csrf_token();
?>

<div class="max-w-4xl mx-auto space-y-6">
    <div>
        <h1 class="text-2xl font-extrabold text-white">Manage Hero Section & Profile</h1>
        <p class="text-xs text-slate-400">Update main headline, title, pitch, profile photo, and work status pill.</p>
    </div>

    <div class="bg-slate-900 p-8 rounded-3xl border border-slate-800 shadow-xl">
        <form method="POST" action="manage-hero.php" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= e($csrf_token) ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="status_text" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Status Pill Indicator</label>
                    <input type="text" id="status_text" name="status_text" value="<?= e($hero['status_text']) ?>" required class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500">
                </div>
                <div>
                    <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Full Name</label>
                    <input type="text" id="name" name="name" value="<?= e($hero['name']) ?>" required class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500">
                </div>
            </div>

            <div>
                <label for="title" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Professional Headline Title</label>
                <input type="text" id="title" name="title" value="<?= e($hero['title']) ?>" required class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500">
            </div>

            <div>
                <label for="subtitle" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Subtitle / Tagline</label>
                <input type="text" id="subtitle" name="subtitle" value="<?= e($hero['subtitle']) ?>" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500">
            </div>

            <div>
                <label for="pitch" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Pitch Paragraph / Bio</label>
                <textarea id="pitch" name="pitch" rows="4" required class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500"><?= e($hero['pitch']) ?></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-800">
                <div>
                    <label for="github_username" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">GitHub Username</label>
                    <input type="text" id="github_username" name="github_username" value="<?= e($hero['github_username'] ?? 'TarikurRahman') ?>" required placeholder="TarikurRahman" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500 font-mono">
                    <p class="text-[11px] text-slate-500 mt-1">Used to pull live contribution graph and commit streaks.</p>
                </div>
                <div>
                    <label for="github_token" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">GitHub Personal Access Token (Optional)</label>
                    <input type="password" id="github_token" name="github_token" value="<?= e($hero['github_token'] ?? '') ?>" placeholder="ghp_xxxxxxxxxxxx (Optional for GraphQL)" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500 font-mono">
                    <p class="text-[11px] text-slate-500 mt-1">Required for private repos or official GraphQL API quota.</p>
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-8 py-3.5 rounded-xl text-sm font-bold bg-emerald-500 hover:bg-emerald-400 text-slate-950 shadow-lg shadow-emerald-500/25 transition-all flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i> Save Hero Changes
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/admin-footer.php'; ?>
