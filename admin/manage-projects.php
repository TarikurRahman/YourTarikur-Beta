<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin_login();

$db = getDB();

$action = $_GET['action'] ?? 'list';
$edit_id = intval($_GET['id'] ?? 0);
$project_data = null;

if ($action === 'delete' && $edit_id > 0) {
    if (verify_csrf_token($_GET['token'] ?? '')) {
        $stmt = $db->prepare("DELETE FROM projects WHERE id = :id");
        $stmt->execute([':id' => $edit_id]);
        set_flash('success', 'Project deleted successfully.');
    } else {
        set_flash('error', 'Security token mismatch.');
    }
    redirect('manage-projects.php');
}

// Handle Form POST for Add / Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        set_flash('error', 'Invalid security token.');
        redirect('manage-projects.php');
    }

    $id = intval($_POST['project_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $full_description = trim($_POST['full_description'] ?? '');
    $tech_stack = trim($_POST['tech_stack'] ?? '');
    $live_url = trim($_POST['live_url'] ?? '#');
    $github_url = trim($_POST['github_url'] ?? '#');
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $slug = slugify($title);
    $image_url = $_POST['existing_image_url'] ?? 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&q=80&w=800';

    try {
        $uploaded = upload_image('image_file', 'uploads');
        if ($uploaded) {
            $image_url = '../' . $uploaded;
        } elseif (!empty($_POST['image_url'])) {
            $image_url = trim($_POST['image_url']);
        }
    } catch (Exception $ex) {
        set_flash('error', $ex->getMessage());
        redirect('manage-projects.php');
    }

    if (empty($title) || empty($description) || empty($tech_stack)) {
        set_flash('error', 'Title, description, and tech stack are required.');
    } else {
        try {
            if ($id > 0) {
                // Update
                $stmt = $db->prepare("UPDATE projects SET 
                    title = :title, 
                    slug = :slug, 
                    description = :description, 
                    full_description = :full_description, 
                    image_url = :image_url, 
                    tech_stack = :tech_stack, 
                    live_url = :live_url, 
                    github_url = :github_url, 
                    is_featured = :is_featured 
                    WHERE id = :id");
                $stmt->execute([
                    ':title' => $title,
                    ':slug' => $slug,
                    ':description' => $description,
                    ':full_description' => $full_description,
                    ':image_url' => $image_url,
                    ':tech_stack' => $tech_stack,
                    ':live_url' => $live_url,
                    ':github_url' => $github_url,
                    ':is_featured' => $is_featured,
                    ':id' => $id
                ]);
                set_flash('success', 'Project updated successfully.');
            } else {
                // Insert
                $stmt = $db->prepare("INSERT INTO projects (title, slug, description, full_description, image_url, tech_stack, live_url, github_url, is_featured) 
                    VALUES (:title, :slug, :description, :full_description, :image_url, :tech_stack, :live_url, :github_url, :is_featured)");
                $stmt->execute([
                    ':title' => $title,
                    ':slug' => $slug,
                    ':description' => $description,
                    ':full_description' => $full_description,
                    ':image_url' => $image_url,
                    ':tech_stack' => $tech_stack,
                    ':live_url' => $live_url,
                    ':github_url' => $github_url,
                    ':is_featured' => $is_featured
                ]);
                set_flash('success', 'New project added successfully.');
            }
            redirect('manage-projects.php');
        } catch (Exception $e) {
            set_flash('error', 'Database error: ' . $e->getMessage());
        }
    }
    redirect('manage-projects.php');
}

// Include Admin Header layout AFTER handling actions
require_once __DIR__ . '/admin-header.php';

$csrf_token = generate_csrf_token();

if ($action === 'edit' && $edit_id > 0) {
    $stmt = $db->prepare("SELECT * FROM projects WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $edit_id]);
    $project_data = $stmt->fetch();
}

$all_projects = get_projects();
?>

<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-white">Manage Projects</h1>
            <p class="text-xs text-slate-400">Add, edit, or remove featured portfolio projects</p>
        </div>
        <?php if ($action !== 'add' && $action !== 'edit'): ?>
            <a href="manage-projects.php?action=add" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-emerald-500 hover:bg-emerald-400 text-slate-950 flex items-center gap-1.5 shadow-lg shadow-emerald-500/20">
                <i data-lucide="plus" class="w-4 h-4"></i> Add New Project
            </a>
        <?php else: ?>
            <a href="manage-projects.php" class="px-4 py-2.5 rounded-xl text-xs font-semibold bg-slate-800 text-slate-300 hover:bg-slate-700">
                &larr; Back to List
            </a>
        <?php endif; ?>
    </div>

    <!-- FORM SECTION: Add or Edit -->
    <?php if ($action === 'add' || $action === 'edit'): ?>
        <div class="bg-slate-900 p-8 rounded-3xl border border-slate-800 shadow-xl">
            <h2 class="text-lg font-bold text-white mb-6"><?= $action === 'edit' ? 'Edit Project' : 'Add New Project' ?></h2>
            <form method="POST" action="manage-projects.php" enctype="multipart/form-data" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token) ?>">
                <input type="hidden" name="project_id" value="<?= e($project_data['id'] ?? 0) ?>">
                <input type="hidden" name="existing_image_url" value="<?= e($project_data['image_url'] ?? '') ?>">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="title" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Project Title</label>
                        <input type="text" id="title" name="title" value="<?= e($project_data['title'] ?? '') ?>" required class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500">
                    </div>
                    <div>
                        <label for="tech_stack" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Tech Stack Tags (Comma-separated)</label>
                        <input type="text" id="tech_stack" name="tech_stack" value="<?= e($project_data['tech_stack'] ?? '') ?>" placeholder="PHP 8, MySQL, React, Tailwind CSS" required class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500">
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Short Description (Summary)</label>
                    <textarea id="description" name="description" rows="3" required class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500"><?= e($project_data['description'] ?? '') ?></textarea>
                </div>

                <div>
                    <label for="full_description" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Full Overview & Case Study Details</label>
                    <textarea id="full_description" name="full_description" rows="5" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500"><?= e($project_data['full_description'] ?? '') ?></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="live_url" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Live Demo URL</label>
                        <input type="text" id="live_url" name="live_url" value="<?= e($project_data['live_url'] ?? '#') ?>" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500">
                    </div>
                    <div>
                        <label for="github_url" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">GitHub Repository URL</label>
                        <input type="text" id="github_url" name="github_url" value="<?= e($project_data['github_url'] ?? '#') ?>" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Project Cover Image</label>
                    <div class="space-y-3">
                        <input type="file" name="image_file" accept="image/*" class="text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-500/10 file:text-emerald-400">
                        <input type="text" name="image_url" placeholder="Or enter image URL" value="<?= e($project_data['image_url'] ?? '') ?>" class="w-full px-4 py-2 rounded-xl bg-slate-950 border border-slate-800 text-xs text-slate-300 focus:border-emerald-500">
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" id="is_featured" name="is_featured" value="1" <?= ($project_data['is_featured'] ?? 1) ? 'checked' : '' ?> class="w-4 h-4 accent-emerald-500 rounded">
                    <label for="is_featured" class="text-xs font-semibold text-slate-300">Feature this project on homepage grid</label>
                </div>

                <div class="pt-4 flex justify-end gap-4">
                    <a href="manage-projects.php" class="px-6 py-3 rounded-xl text-xs font-semibold bg-slate-800 text-slate-300 hover:bg-slate-700">Cancel</a>
                    <button type="submit" class="px-8 py-3 rounded-xl text-xs font-bold bg-emerald-500 hover:bg-emerald-400 text-slate-950 shadow-lg shadow-emerald-500/20">
                        Save Project
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <!-- LIST TABLE SECTION -->
    <div class="bg-slate-900 rounded-3xl border border-slate-800 overflow-hidden shadow-xl">
        <div class="p-6 border-b border-slate-800">
            <h2 class="text-lg font-bold text-white">All Projects (<?= count($all_projects) ?>)</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950 text-slate-400 uppercase text-[10px] font-mono border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Preview</th>
                        <th class="py-3 px-4">Title</th>
                        <th class="py-3 px-4">Tech Stack</th>
                        <th class="py-3 px-4">Featured</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    <?php foreach ($all_projects as $p): ?>
                        <tr class="hover:bg-slate-950/50">
                            <td class="py-3 px-4">
                                <img src="<?= e($p['image_url']) ?>" alt="" class="w-12 h-8 rounded object-cover border border-slate-800">
                            </td>
                            <td class="py-3 px-4 font-bold text-white">
                                <?= e($p['title']) ?>
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-400">
                                <?= e($p['tech_stack']) ?>
                            </td>
                            <td class="py-3 px-4">
                                <?php if ($p['is_featured']): ?>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Featured</span>
                                <?php else: ?>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-800 text-slate-500">Standard</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 text-right space-x-2">
                                <a href="manage-projects.php?action=edit&id=<?= $p['id'] ?>" class="text-emerald-400 hover:underline font-semibold">Edit</a>
                                <a href="manage-projects.php?action=delete&id=<?= $p['id'] ?>&token=<?= $csrf_token ?>" onclick="return confirm('Are you sure you want to delete this project?')" class="text-rose-400 hover:underline font-semibold">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/admin-footer.php'; ?>
