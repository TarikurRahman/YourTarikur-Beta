<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin_login();

$db = getDB();

$action = $_GET['action'] ?? 'list';
$edit_id = intval($_GET['id'] ?? 0);
$article_data = null;

if ($action === 'delete' && $edit_id > 0) {
    if (verify_csrf_token($_GET['token'] ?? '')) {
        $stmt = $db->prepare("DELETE FROM articles WHERE id = :id");
        $stmt->execute([':id' => $edit_id]);
        set_flash('success', 'Article deleted successfully.');
    } else {
        set_flash('error', 'Security token mismatch.');
    }
    redirect('manage-articles.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        set_flash('error', 'Invalid security token.');
        redirect('manage-articles.php');
    }

    $id = intval($_POST['article_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $excerpt = trim($_POST['excerpt'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $read_time = trim($_POST['read_time'] ?? '5 min read');
    $published_at = $_POST['published_at'] ?? date('Y-m-d');
    $is_published = isset($_POST['is_published']) ? 1 : 0;
    $slug = slugify($title);
    $thumbnail = $_POST['existing_thumbnail'] ?? 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?auto=format&fit=crop&q=80&w=800';

    try {
        $uploaded = upload_image('thumbnail_file', 'uploads');
        if ($uploaded) {
            $thumbnail = '../' . $uploaded;
        } elseif (!empty($_POST['thumbnail_url'])) {
            $thumbnail = trim($_POST['thumbnail_url']);
        }
    } catch (Exception $ex) {
        set_flash('error', $ex->getMessage());
        redirect('manage-articles.php');
    }

    if (empty($title) || empty($excerpt) || empty($content)) {
        set_flash('error', 'Title, excerpt, and full content are required.');
    } else {
        try {
            if ($id > 0) {
                $stmt = $db->prepare("UPDATE articles SET 
                    title = :title, 
                    slug = :slug, 
                    excerpt = :excerpt, 
                    content = :content, 
                    thumbnail = :thumbnail, 
                    read_time = :read_time, 
                    published_at = :published_at, 
                    is_published = :is_published 
                    WHERE id = :id");
                $stmt->execute([
                    ':title' => $title,
                    ':slug' => $slug,
                    ':excerpt' => $excerpt,
                    ':content' => $content,
                    ':thumbnail' => $thumbnail,
                    ':read_time' => $read_time,
                    ':published_at' => $published_at,
                    ':is_published' => $is_published,
                    ':id' => $id
                ]);
                set_flash('success', 'Article updated successfully.');
            } else {
                $stmt = $db->prepare("INSERT INTO articles (title, slug, excerpt, content, thumbnail, read_time, published_at, is_published) 
                    VALUES (:title, :slug, :excerpt, :content, :thumbnail, :read_time, :published_at, :is_published)");
                $stmt->execute([
                    ':title' => $title,
                    ':slug' => $slug,
                    ':excerpt' => $excerpt,
                    ':content' => $content,
                    ':thumbnail' => $thumbnail,
                    ':read_time' => $read_time,
                    ':published_at' => $published_at,
                    ':is_published' => $is_published
                ]);
                set_flash('success', 'New article published successfully.');
            }
            redirect('manage-articles.php');
        } catch (Exception $e) {
            set_flash('error', 'Database error: ' . $e->getMessage());
        }
    }
    redirect('manage-articles.php');
}

// Render HTML layout AFTER handling actions
require_once __DIR__ . '/admin-header.php';

$csrf_token = generate_csrf_token();

if ($action === 'edit' && $edit_id > 0) {
    $stmt = $db->prepare("SELECT * FROM articles WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $edit_id]);
    $article_data = $stmt->fetch();
}

$all_articles = get_articles(false);
?>

<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-white">Manage Blog & Articles</h1>
            <p class="text-xs text-slate-400">Write, edit, and publish technical insights and articles</p>
        </div>
        <?php if ($action !== 'add' && $action !== 'edit'): ?>
            <a href="manage-articles.php?action=add" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-emerald-500 hover:bg-emerald-400 text-slate-950 flex items-center gap-1.5 shadow-lg shadow-emerald-500/20">
                <i data-lucide="plus" class="w-4 h-4"></i> Write New Article
            </a>
        <?php else: ?>
            <a href="manage-articles.php" class="px-4 py-2.5 rounded-xl text-xs font-semibold bg-slate-800 text-slate-300 hover:bg-slate-700">
                &larr; Back to Articles List
            </a>
        <?php endif; ?>
    </div>

    <!-- FORM SECTION: Add or Edit -->
    <?php if ($action === 'add' || $action === 'edit'): ?>
        <div class="bg-slate-900 p-8 rounded-3xl border border-slate-800 shadow-xl">
            <h2 class="text-lg font-bold text-white mb-6"><?= $action === 'edit' ? 'Edit Article' : 'Write New Article' ?></h2>
            <form method="POST" action="manage-articles.php" enctype="multipart/form-data" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token) ?>">
                <input type="hidden" name="article_id" value="<?= e($article_data['id'] ?? 0) ?>">
                <input type="hidden" name="existing_thumbnail" value="<?= e($article_data['thumbnail'] ?? '') ?>">

                <div>
                    <label for="title" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Article Headline Title</label>
                    <input type="text" id="title" name="title" value="<?= e($article_data['title'] ?? '') ?>" required class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="read_time" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Reading Time Tag</label>
                        <input type="text" id="read_time" name="read_time" value="<?= e($article_data['read_time'] ?? '5 min read') ?>" required class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500">
                    </div>
                    <div>
                        <label for="published_at" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Publication Date</label>
                        <input type="date" id="published_at" name="published_at" value="<?= e($article_data['published_at'] ?? date('Y-m-d')) ?>" required class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500">
                    </div>
                </div>

                <div>
                    <label for="excerpt" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Short Summary / Excerpt</label>
                    <textarea id="excerpt" name="excerpt" rows="2" required class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500"><?= e($article_data['excerpt'] ?? '') ?></textarea>
                </div>

                <div>
                    <label for="content" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Full Article Body Content</label>
                    <textarea id="content" name="content" rows="10" required class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500 font-mono"><?= e($article_data['content'] ?? '') ?></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Featured Thumbnail Image</label>
                    <div class="space-y-3">
                        <input type="file" name="thumbnail_file" accept="image/*" class="text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-500/10 file:text-emerald-400">
                        <input type="text" name="thumbnail_url" placeholder="Or enter image URL" value="<?= e($article_data['thumbnail'] ?? '') ?>" class="w-full px-4 py-2 rounded-xl bg-slate-950 border border-slate-800 text-xs text-slate-300 focus:border-emerald-500">
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" id="is_published" name="is_published" value="1" <?= ($article_data['is_published'] ?? 1) ? 'checked' : '' ?> class="w-4 h-4 accent-emerald-500 rounded">
                    <label for="is_published" class="text-xs font-semibold text-slate-300">Publish immediately to public blog</label>
                </div>

                <div class="pt-4 flex justify-end gap-4">
                    <a href="manage-articles.php" class="px-6 py-3 rounded-xl text-xs font-semibold bg-slate-800 text-slate-300 hover:bg-slate-700">Cancel</a>
                    <button type="submit" class="px-8 py-3 rounded-xl text-xs font-bold bg-emerald-500 hover:bg-emerald-400 text-slate-950 shadow-lg shadow-emerald-500/20">
                        Save Article
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <!-- LIST TABLE SECTION -->
    <div class="bg-slate-900 rounded-3xl border border-slate-800 overflow-hidden shadow-xl">
        <div class="p-6 border-b border-slate-800">
            <h2 class="text-lg font-bold text-white">All Articles (<?= count($all_articles) ?>)</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950 text-slate-400 uppercase text-[10px] font-mono border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Date</th>
                        <th class="py-3 px-4">Title</th>
                        <th class="py-3 px-4">Views</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    <?php foreach ($all_articles as $art): ?>
                        <tr class="hover:bg-slate-950/50">
                            <td class="py-3 px-4 font-mono text-slate-400"><?= e($art['published_at']) ?></td>
                            <td class="py-3 px-4 font-bold text-white max-w-xs truncate">
                                <?= e($art['title']) ?>
                            </td>
                            <td class="py-3 px-4 font-mono text-emerald-400"><?= e($art['views']) ?></td>
                            <td class="py-3 px-4">
                                <?php if ($art['is_published']): ?>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Published</span>
                                <?php else: ?>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">Draft</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 text-right space-x-2">
                                <a href="../article-details.php?slug=<?= e($art['slug']) ?>" target="_blank" class="text-slate-400 hover:text-white">View</a>
                                <a href="manage-articles.php?action=edit&id=<?= $art['id'] ?>" class="text-emerald-400 hover:underline font-semibold">Edit</a>
                                <a href="manage-articles.php?action=delete&id=<?= $art['id'] ?>&token=<?= $csrf_token ?>" onclick="return confirm('Are you sure you want to delete this article?')" class="text-rose-400 hover:underline font-semibold">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/admin-footer.php'; ?>
