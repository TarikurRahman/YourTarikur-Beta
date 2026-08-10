<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin_login();

$db = getDB();

if (isset($_GET['delete']) && intval($_GET['delete']) > 0) {
    if (verify_csrf_token($_GET['token'] ?? '')) {
        $stmt = $db->prepare("DELETE FROM life_gallery WHERE id = :id");
        $stmt->execute([':id' => intval($_GET['delete'])]);
        set_flash('success', 'Polaroid image removed from gallery.');
    }
    redirect('manage-gallery.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Invalid security token.');
        redirect('manage-gallery.php');
    }

    $title = trim($_POST['title'] ?? '');
    $caption = trim($_POST['caption'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $image_url = 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&q=80&w=600';

    try {
        $uploaded = upload_image('image_file', 'uploads');
        if ($uploaded) {
            $image_url = '../' . $uploaded;
        } elseif (!empty($_POST['image_url'])) {
            $image_url = trim($_POST['image_url']);
        }
    } catch (Exception $ex) {
        set_flash('error', $ex->getMessage());
        redirect('manage-gallery.php');
    }

    if (empty($title)) {
        set_flash('error', 'Photo title is required.');
    } else {
        $stmt = $db->prepare("INSERT INTO life_gallery (title, caption, image_url, location) VALUES (:title, :caption, :image_url, :location)");
        $stmt->execute([
            ':title' => $title,
            ':caption' => $caption,
            ':image_url' => $image_url,
            ':location' => $location
        ]);
        set_flash('success', 'New polaroid photo added to gallery.');
    }
    redirect('manage-gallery.php');
}

// Include Admin Header layout AFTER handling actions
require_once __DIR__ . '/admin-header.php';

$csrf_token = generate_csrf_token();
$gallery = get_life_gallery();
?>

<div class="space-y-8">
    <div>
        <h1 class="text-2xl font-extrabold text-white">Manage "Life Beyond Code" Gallery</h1>
        <p class="text-xs text-slate-400">Upload and curate personal photos, travels, and hobby polaroids</p>
    </div>

    <!-- Add Photo Form -->
    <div class="bg-slate-900 p-8 rounded-3xl border border-slate-800 shadow-xl">
        <h2 class="text-lg font-bold text-white mb-6">Add New Polaroid Photo</h2>
        <form method="POST" action="manage-gallery.php" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= e($csrf_token) ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="title" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Title</label>
                    <input type="text" id="title" name="title" placeholder="Sylhet Tea Trek" required class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500">
                </div>
                <div>
                    <label for="location" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Location Badge</label>
                    <input type="text" id="location" name="location" placeholder="Sylhet, Bangladesh" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500">
                </div>
            </div>

            <div>
                <label for="caption" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Caption / Short Story</label>
                <input type="text" id="caption" name="caption" placeholder="Exploring tea gardens during a weekend getaway..." class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Polaroid Photo Image</label>
                <div class="space-y-3">
                    <input type="file" name="image_file" accept="image/*" class="text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-500/10 file:text-emerald-400">
                    <input type="text" name="image_url" placeholder="Or enter image URL" class="w-full px-4 py-2 rounded-xl bg-slate-950 border border-slate-800 text-xs text-slate-300 focus:border-emerald-500">
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-8 py-3 rounded-xl text-xs font-bold bg-emerald-500 text-slate-950 hover:bg-emerald-400 shadow-lg shadow-emerald-500/20">
                    Add to Polaroid Gallery
                </button>
            </div>
        </form>
    </div>

    <!-- Gallery Grid Preview -->
    <div class="bg-slate-900 p-8 rounded-3xl border border-slate-800 shadow-xl">
        <h2 class="text-lg font-bold text-white mb-6">Current Gallery Items (<?= count($gallery) ?>)</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            <?php foreach ($gallery as $g): ?>
                <div class="bg-slate-950 p-4 rounded-2xl border border-slate-800 flex flex-col justify-between">
                    <div class="aspect-[4/3] rounded-xl overflow-hidden mb-3 bg-slate-900">
                        <img src="<?= e($g['image_url']) ?>" alt="" class="w-full h-full object-cover">
                    </div>
                    <div class="space-y-1">
                        <h4 class="font-bold text-sm text-white"><?= e($g['title']) ?></h4>
                        <p class="text-xs text-slate-400 line-clamp-2"><?= e($g['caption']) ?></p>
                        <?php if (!empty($g['location'])): ?>
                            <span class="inline-block text-[10px] text-emerald-400 font-mono"><?= e($g['location']) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-900 flex justify-end">
                        <a href="manage-gallery.php?delete=<?= $g['id'] ?>&token=<?= $csrf_token ?>" onclick="return confirm('Delete this photo?')" class="text-xs text-rose-400 hover:underline font-semibold">Delete Photo</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/admin-footer.php'; ?>
