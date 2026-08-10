<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin_login();

$db = getDB();

// Handle Service Save / Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_type']) && $_POST['form_type'] === 'service') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Invalid security token.');
        redirect('manage-services.php');
    }

    $id = intval($_POST['service_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $icon = trim($_POST['icon'] ?? 'code');
    $order = intval($_POST['display_order'] ?? 0);

    if ($id > 0) {
        $stmt = $db->prepare("UPDATE services SET title = :title, description = :description, icon = :icon, display_order = :order WHERE id = :id");
        $stmt->execute([':title' => $title, ':description' => $description, ':icon' => $icon, ':order' => $order, ':id' => $id]);
        set_flash('success', 'Service updated successfully.');
    } else {
        $stmt = $db->prepare("INSERT INTO services (title, description, icon, display_order) VALUES (:title, :description, :icon, :order)");
        $stmt->execute([':title' => $title, ':description' => $description, ':icon' => $icon, ':order' => $order]);
        set_flash('success', 'New service created.');
    }
    redirect('manage-services.php');
}

// Handle Stat Save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_type']) && $_POST['form_type'] === 'stat') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Invalid security token.');
        redirect('manage-services.php');
    }

    $id = intval($_POST['stat_id'] ?? 0);
    $stat_value = trim($_POST['stat_value'] ?? '');
    $stat_label = trim($_POST['stat_label'] ?? '');
    $icon = trim($_POST['icon'] ?? 'award');

    if ($id > 0) {
        $stmt = $db->prepare("UPDATE stats SET stat_value = :val, stat_label = :lbl, icon = :icon WHERE id = :id");
        $stmt->execute([':val' => $stat_value, ':lbl' => $stat_label, ':icon' => $icon, ':id' => $id]);
        set_flash('success', 'Metric stat updated.');
    }
    redirect('manage-services.php');
}

if (isset($_GET['delete_service'])) {
    $del_id = intval($_GET['delete_service']);
    if (verify_csrf_token($_GET['token'] ?? '')) {
        $stmt = $db->prepare("DELETE FROM services WHERE id = :id");
        $stmt->execute([':id' => $del_id]);
        set_flash('success', 'Service deleted.');
    }
    redirect('manage-services.php');
}

// Include Admin Header layout AFTER handling actions
require_once __DIR__ . '/admin-header.php';

$csrf_token = generate_csrf_token();
$services = get_services();
$stats = get_stats();
?>

<div class="space-y-10">
    <div>
        <h1 class="text-2xl font-extrabold text-white">Manage Services & Key Metrics</h1>
        <p class="text-xs text-slate-400">Control services offered and counter bar stats displayed on the home page</p>
    </div>

    <!-- STATS COUNTER BAR MANAGER -->
    <div class="bg-slate-900 p-8 rounded-3xl border border-slate-800 shadow-xl space-y-6">
        <h2 class="text-lg font-bold text-white flex items-center gap-2">
            <i data-lucide="award" class="w-5 h-5 text-emerald-400"></i> Key Metrics Bar Stats
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($stats as $st): ?>
                <form method="POST" action="manage-services.php" class="bg-slate-950 p-5 rounded-2xl border border-slate-800 space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= e($csrf_token) ?>">
                    <input type="hidden" name="form_type" value="stat">
                    <input type="hidden" name="stat_id" value="<?= e($st['id']) ?>">

                    <div>
                        <label class="block text-[10px] font-mono text-slate-500 uppercase">Stat Key: <?= e($st['stat_key']) ?></label>
                        <input type="text" name="stat_value" value="<?= e($st['stat_value']) ?>" required class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-white font-extrabold text-lg text-emerald-400 mt-1">
                    </div>

                    <div>
                        <label class="block text-[10px] font-mono text-slate-500 uppercase">Label Text</label>
                        <input type="text" name="stat_label" value="<?= e($st['stat_label']) ?>" required class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-white text-xs mt-1">
                    </div>

                    <div>
                        <label class="block text-[10px] font-mono text-slate-500 uppercase">Lucide Icon Name</label>
                        <input type="text" name="icon" value="<?= e($st['icon']) ?>" required class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 text-xs mt-1">
                    </div>

                    <button type="submit" class="w-full py-2 rounded-xl text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500 hover:text-slate-950 transition-all">
                        Update Stat
                    </button>
                </form>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- SERVICES MANAGER -->
    <div class="bg-slate-900 p-8 rounded-3xl border border-slate-800 shadow-xl space-y-6">
        <h2 class="text-lg font-bold text-white flex items-center gap-2">
            <i data-lucide="layers" class="w-5 h-5 text-emerald-400"></i> Service Offerings
        </h2>

        <!-- Add/Edit Service Form -->
        <form method="POST" action="manage-services.php" class="bg-slate-950 p-6 rounded-2xl border border-slate-800 space-y-4">
            <input type="hidden" name="csrf_token" value="<?= e($csrf_token) ?>">
            <input type="hidden" name="form_type" value="service">
            <input type="hidden" name="service_id" id="service_id" value="0">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold uppercase text-slate-300 mb-1">Service Title</label>
                    <input type="text" name="title" id="svc_title" required placeholder="Full Stack Web Development" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-300 mb-1">Lucide Icon Name</label>
                    <input type="text" name="icon" id="svc_icon" required placeholder="code-2, server, bot, zap" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-300 mb-1">Description</label>
                <textarea name="description" id="svc_desc" rows="2" required placeholder="Detailed description of what you deliver..." class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-sm"></textarea>
            </div>

            <div class="flex justify-end gap-3">
                <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold bg-emerald-500 text-slate-950 hover:bg-emerald-400">
                    Save Service
                </button>
            </div>
        </form>

        <!-- Services Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950 text-slate-400 uppercase text-[10px] font-mono border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Icon</th>
                        <th class="py-3 px-4">Title</th>
                        <th class="py-3 px-4">Description</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    <?php foreach ($services as $svc): ?>
                        <tr class="hover:bg-slate-950/50">
                            <td class="py-3 px-4 font-mono text-emerald-400">
                                <i data-lucide="<?= e($svc['icon']) ?>" class="w-5 h-5"></i>
                            </td>
                            <td class="py-3 px-4 font-bold text-white"><?= e($svc['title']) ?></td>
                            <td class="py-3 px-4 text-slate-400 max-w-md truncate"><?= e($svc['description']) ?></td>
                            <td class="py-3 px-4 text-right space-x-2">
                                <a href="manage-services.php?delete_service=<?= $svc['id'] ?>&token=<?= $csrf_token ?>" onclick="return confirm('Delete this service?')" class="text-rose-400 hover:underline">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/admin-footer.php'; ?>
