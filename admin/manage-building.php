<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin_login();

$db = getDB();

if (isset($_GET['delete']) && intval($_GET['delete']) > 0) {
    if (verify_csrf_token($_GET['token'] ?? '')) {
        $stmt = $db->prepare("DELETE FROM currently_building WHERE id = :id");
        $stmt->execute([':id' => intval($_GET['delete'])]);
        set_flash('success', 'Building goal item removed.');
    }
    redirect('manage-building.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Invalid security token.');
        redirect('manage-building.php');
    }

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status = trim($_POST['status'] ?? 'In Progress');
    $progress = intval($_POST['progress_percent'] ?? 50);
    $tech_stack = trim($_POST['tech_stack'] ?? '');
    $icon = trim($_POST['icon'] ?? 'terminal');

    if (empty($title) || empty($description)) {
        set_flash('error', 'Title and description are required.');
    } else {
        $stmt = $db->prepare("INSERT INTO currently_building (title, description, status, progress_percent, tech_stack, icon) VALUES (:title, :description, :status, :progress, :tech, :icon)");
        $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':status' => $status,
            ':progress' => $progress,
            ':tech' => $tech_stack,
            ':icon' => $icon
        ]);
        set_flash('success', 'New exploration goal added.');
    }
    redirect('manage-building.php');
}

// Include Admin Header layout AFTER handling actions
require_once __DIR__ . '/admin-header.php';

$csrf_token = generate_csrf_token();
$building = get_currently_building();
?>

<div class="space-y-8">
    <div>
        <h1 class="text-2xl font-extrabold text-white">Manage "Currently Exploring & Building"</h1>
        <p class="text-xs text-slate-400">Track active projects under development and continuous learning progress</p>
    </div>

    <!-- Form -->
    <div class="bg-slate-900 p-8 rounded-3xl border border-slate-800 shadow-xl">
        <h2 class="text-lg font-bold text-white mb-6">Add New Exploration Item</h2>
        <form method="POST" action="manage-building.php" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= e($csrf_token) ?>">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-2">
                    <label for="title" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Item Title</label>
                    <input type="text" id="title" name="title" required placeholder="Autonomous AI Workflow Agents" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500">
                </div>
                <div>
                    <label for="status" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Status Pill</label>
                    <input type="text" id="status" name="status" placeholder="Active Build / R&D" required class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500">
                </div>
            </div>

            <div>
                <label for="description" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Description</label>
                <textarea id="description" name="description" rows="3" required placeholder="Details about what you are experimenting with..." class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500"></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="progress_percent" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Progress Percentage (0 - 100)</label>
                    <input type="number" id="progress_percent" name="progress_percent" min="0" max="100" value="75" required class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500">
                </div>
                <div>
                    <label for="tech_stack" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Tech Stack Note</label>
                    <input type="text" id="tech_stack" name="tech_stack" placeholder="PHP 8.2, cURL, LLM API" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500">
                </div>
                <div>
                    <label for="icon" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Lucide Icon Name</label>
                    <input type="text" id="icon" name="icon" value="terminal" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500">
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-8 py-3 rounded-xl text-xs font-bold bg-emerald-500 text-slate-950 hover:bg-emerald-400 shadow-lg shadow-emerald-500/20">
                    Add Exploration Goal
                </button>
            </div>
        </form>
    </div>

    <!-- Items Grid -->
    <div class="bg-slate-900 p-8 rounded-3xl border border-slate-800 shadow-xl">
        <h2 class="text-lg font-bold text-white mb-6">Current Items (<?= count($building) ?>)</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php foreach ($building as $b): ?>
                <div class="bg-slate-950 p-5 rounded-2xl border border-slate-800 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[10px] font-semibold text-emerald-400 uppercase font-mono"><?= e($b['status']) ?></span>
                            <span class="text-xs font-bold text-white"><?= e($b['progress_percent']) ?>%</span>
                        </div>
                        <h3 class="font-bold text-base text-white"><?= e($b['title']) ?></h3>
                        <p class="text-xs text-slate-400 mt-2 line-clamp-3"><?= e($b['description']) ?></p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-900 flex justify-end">
                        <a href="manage-building.php?delete=<?= $b['id'] ?>&token=<?= $csrf_token ?>" onclick="return confirm('Delete this item?')" class="text-xs text-rose-400 hover:underline font-semibold">Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/admin-footer.php'; ?>
