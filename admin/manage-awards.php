<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin_login();

$db = getDB();

// Handle Award Save (Insert / Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Invalid security token.');
        redirect_to('manage-awards.php');
    }

    $id = intval($_POST['award_id'] ?? 0);
    $category = trim($_POST['category'] ?? 'National & International');
    $title = trim($_POST['title'] ?? '');
    $team_name = trim($_POST['team_name'] ?? '');
    $institution = trim($_POST['institution'] ?? '');
    $event_date = trim($_POST['event_date'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $organizer = trim($_POST['organizer'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $display_order = intval($_POST['display_order'] ?? 0);

    if (empty($title)) {
        set_flash('error', 'Award title is required.');
    } else {
        if ($id > 0) {
            $stmt = $db->prepare("UPDATE awards SET 
                category = :category,
                title = :title,
                team_name = :team_name,
                institution = :institution,
                event_date = :event_date,
                location = :location,
                organizer = :organizer,
                description = :description,
                display_order = :display_order
                WHERE id = :id");
            $stmt->execute([
                ':category' => $category,
                ':title' => $title,
                ':team_name' => $team_name,
                ':institution' => $institution,
                ':event_date' => $event_date,
                ':location' => $location,
                ':organizer' => $organizer,
                ':description' => $description,
                ':display_order' => $display_order,
                ':id' => $id
            ]);
            set_flash('success', 'Award updated successfully.');
        } else {
            $stmt = $db->prepare("INSERT INTO awards (category, title, team_name, institution, event_date, location, organizer, description, display_order) 
                VALUES (:category, :title, :team_name, :institution, :event_date, :location, :organizer, :description, :display_order)");
            $stmt->execute([
                ':category' => $category,
                ':title' => $title,
                ':team_name' => $team_name,
                ':institution' => $institution,
                ':event_date' => $event_date,
                ':location' => $location,
                ':organizer' => $organizer,
                ':description' => $description,
                ':display_order' => $display_order
            ]);
            set_flash('success', 'New award added successfully.');
        }
    }
    redirect_to('manage-awards.php');
}

// Handle Delete
if (isset($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    if (verify_csrf_token($_GET['token'] ?? '')) {
        $stmt = $db->prepare("DELETE FROM awards WHERE id = :id");
        $stmt->execute([':id' => $del_id]);
        set_flash('success', 'Award deleted.');
    } else {
        set_flash('error', 'Security check failed.');
    }
    redirect_to('manage-awards.php');
}

// Include header layout AFTER processing requests
require_once __DIR__ . '/admin-header.php';

$csrf_token = generate_csrf_token();

// Fetch single award for edit
$edit_award = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $stmt = $db->prepare("SELECT * FROM awards WHERE id = :id");
    $stmt->execute([':id' => $edit_id]);
    $edit_award = $stmt->fetch();
}

$awards = $db->query("SELECT * FROM awards ORDER BY display_order ASC, id ASC")->fetchAll();
?>

<div class="space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white font-heading">Manage Awards & Achievements</h1>
            <p class="text-xs text-slate-400 mt-1">Add, edit, or reorder hackathons, medals, and national science fair awards.</p>
        </div>
    </div>

    <!-- Add / Edit Form -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <h2 class="text-sm font-semibold uppercase tracking-wider text-emerald-400 mb-6">
            <?= $edit_award ? 'Edit Award #' . $edit_award['id'] : 'Add New Award / Recognition' ?>
        </h2>
        <form action="manage-awards.php" method="POST" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= e($csrf_token) ?>">
            <input type="hidden" name="award_id" value="<?= e($edit_award['id'] ?? 0) ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="title" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Award Title</label>
                    <input type="text" id="title" name="title" value="<?= e($edit_award['title'] ?? '') ?>" required placeholder="e.g. Gold Medalist - 8th World Invention Competition" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500">
                </div>

                <div>
                    <label for="category" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Category</label>
                    <select id="category" name="category" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500">
                        <option value="National & International" <?= ($edit_award['category'] ?? '') === 'National & International' ? 'selected' : '' ?>>National & International</option>
                        <option value="Divisional & District" <?= ($edit_award['category'] ?? '') === 'Divisional & District' ? 'selected' : '' ?>>Divisional & District</option>
                        <option value="Hackathon & Contests" <?= ($edit_award['category'] ?? '') === 'Hackathon & Contests' ? 'selected' : '' ?>>Hackathon & Contests</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="team_name" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Team Name</label>
                    <input type="text" id="team_name" name="team_name" value="<?= e($edit_award['team_name'] ?? 'Team DEMON71') ?>" placeholder="e.g. Team DEMON71" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500">
                </div>

                <div>
                    <label for="institution" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Institution / College</label>
                    <input type="text" id="institution" name="institution" value="<?= e($edit_award['institution'] ?? '') ?>" placeholder="e.g. Alif Subhan Chowdhury Gov College" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500">
                </div>

                <div>
                    <label for="event_date" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Event Date</label>
                    <input type="text" id="event_date" name="event_date" value="<?= e($edit_award['event_date'] ?? '') ?>" placeholder="e.g. 9 May 2026" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="location" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Location Venue</label>
                    <input type="text" id="location" name="location" value="<?= e($edit_award['location'] ?? '') ?>" placeholder="e.g. Agargaon, Dhaka" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500">
                </div>

                <div>
                    <label for="organizer" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Organizer</label>
                    <input type="text" id="organizer" name="organizer" value="<?= e($edit_award['organizer'] ?? '') ?>" placeholder="e.g. NMST & IYSA" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500">
                </div>

                <div>
                    <label for="display_order" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Display Order</label>
                    <input type="number" id="display_order" name="display_order" value="<?= e($edit_award['display_order'] ?? 1) ?>" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500">
                </div>
            </div>

            <div>
                <label for="description" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Short Description / Project Details</label>
                <textarea id="description" name="description" rows="3" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500" placeholder="Brief details about the award, project, or competition..."><?= e($edit_award['description'] ?? '') ?></textarea>
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="px-6 py-3 rounded-xl text-xs font-bold uppercase tracking-wider bg-emerald-500 text-slate-950 hover:bg-emerald-400 transition-all shadow-lg shadow-emerald-500/20">
                    <?= $edit_award ? 'Save Changes' : 'Create Award' ?>
                </button>
                <?php if ($edit_award): ?>
                    <a href="manage-awards.php" class="px-6 py-3 rounded-xl text-xs font-bold uppercase tracking-wider bg-slate-800 text-slate-300 hover:bg-slate-700 transition-all">Cancel</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Awards List Table -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <h2 class="text-sm font-semibold uppercase tracking-wider text-emerald-400 mb-6">Existing Awards & Achievements</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950 uppercase text-[10px] tracking-wider text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="p-4">Order</th>
                        <th class="p-4">Title & Details</th>
                        <th class="p-4">Category</th>
                        <th class="p-4">Team & Institution</th>
                        <th class="p-4">Date & Location</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    <?php if (empty($awards)): ?>
                        <tr><td colspan="6" class="p-4 text-center text-slate-500">No awards added yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($awards as $aw): ?>
                            <tr class="hover:bg-slate-950/40">
                                <td class="p-4 font-mono font-bold text-emerald-400">#<?= e($aw['display_order']) ?></td>
                                <td class="p-4">
                                    <div class="font-bold text-white text-sm"><?= e($aw['title']) ?></div>
                                    <div class="text-[11px] text-slate-400 mt-0.5 line-clamp-1"><?= e($aw['description']) ?></div>
                                </td>
                                <td class="p-4">
                                    <span class="px-2.5 py-1 rounded-md text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        <?= e($aw['category']) ?>
                                    </span>
                                </td>
                                <td class="p-4">
                                    <div class="text-slate-200 font-semibold"><?= e($aw['team_name']) ?></div>
                                    <div class="text-[11px] text-slate-400"><?= e($aw['institution']) ?></div>
                                </td>
                                <td class="p-4 text-slate-400">
                                    <div><?= e($aw['event_date']) ?></div>
                                    <div class="text-[11px] text-slate-500"><?= e($aw['location']) ?></div>
                                </td>
                                <td class="p-4 text-right space-x-2">
                                    <a href="manage-awards.php?edit=<?= $aw['id'] ?>" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-white font-medium text-[11px] inline-flex items-center gap-1">
                                        <i data-lucide="pencil" class="w-3.5 h-3.5"></i> Edit
                                    </a>
                                    <a href="manage-awards.php?delete=<?= $aw['id'] ?>&token=<?= $csrf_token ?>" onclick="return confirm('Are you sure you want to delete this award?')" class="px-3 py-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 font-medium text-[11px] inline-flex items-center gap-1">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/admin-footer.php'; ?>
