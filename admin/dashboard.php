<?php
require_once __DIR__ . '/admin-header.php';

$db = getDB();

// Count totals
$total_projects = $db->query("SELECT COUNT(*) FROM projects")->fetchColumn();
$total_awards = $db->query("SELECT COUNT(*) FROM awards")->fetchColumn();
$total_articles = $db->query("SELECT COUNT(*) FROM articles")->fetchColumn();
$total_messages = $db->query("SELECT COUNT(*) FROM messages")->fetchColumn();
$unread_messages = $db->query("SELECT COUNT(*) FROM messages WHERE is_read = 0")->fetchColumn();
$total_gallery = $db->query("SELECT COUNT(*) FROM life_gallery")->fetchColumn();
$total_views = $db->query("SELECT SUM(views) FROM articles")->fetchColumn() ?: 0;

// Fetch latest messages
$stmt_msg = $db->query("SELECT * FROM messages ORDER BY created_at DESC LIMIT 5");
$recent_messages = $stmt_msg->fetchAll();
?>

<div class="space-y-8">
    <!-- Header Title -->
    <div>
        <h1 class="text-2xl font-extrabold text-white">Dashboard Overview</h1>
        <p class="text-xs text-slate-400">Welcome back, <?= e($_SESSION['admin_name'] ?? 'Admin') ?>! Here is a summary of your portfolio data.</p>
    </div>

    <!-- Overview Counters Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex items-center justify-center shrink-0">
                <i data-lucide="briefcase" class="w-6 h-6"></i>
            </div>
            <div>
                <div class="text-2xl font-extrabold text-white"><?= $total_projects ?></div>
                <div class="text-xs text-slate-400 font-medium">Projects</div>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-400 border border-amber-500/20 flex items-center justify-center shrink-0">
                <i data-lucide="trophy" class="w-6 h-6"></i>
            </div>
            <div>
                <div class="text-2xl font-extrabold text-white"><?= $total_awards ?></div>
                <div class="text-xs text-slate-400 font-medium">Awards & Honors</div>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-teal-500/10 text-teal-400 border border-teal-500/20 flex items-center justify-center shrink-0">
                <i data-lucide="file-text" class="w-6 h-6"></i>
            </div>
            <div>
                <div class="text-2xl font-extrabold text-white"><?= $total_articles ?></div>
                <div class="text-xs text-slate-400 font-medium">Articles (<?= $total_views ?> views)</div>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-500/10 text-purple-400 border border-purple-500/20 flex items-center justify-center shrink-0">
                <i data-lucide="mail" class="w-6 h-6"></i>
            </div>
            <div>
                <div class="text-2xl font-extrabold text-white"><?= $unread_messages ?> / <?= $total_messages ?></div>
                <div class="text-xs text-slate-400 font-medium">Unread Messages</div>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 flex items-center justify-center shrink-0">
                <i data-lucide="check-circle" class="w-6 h-6"></i>
            </div>
            <div>
                <div class="text-2xl font-extrabold text-emerald-400">PDO Active</div>
                <div class="text-xs text-slate-400 font-medium">Database System</div>
            </div>
        </div>
    </div>

    <!-- Management Action Quick Grid -->
    <div class="bg-slate-900 p-6 rounded-3xl border border-slate-800">
        <h2 class="text-base font-bold text-white mb-4">Quick Management Actions</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs font-semibold">
            <a href="manage-hero.php" class="p-4 rounded-xl bg-slate-950 hover:bg-slate-800 border border-slate-800 text-slate-300 hover:text-emerald-400 transition-all flex flex-col items-center justify-center gap-2">
                <i data-lucide="user-check" class="w-6 h-6 text-emerald-400"></i> Edit Hero & GitHub
            </a>
            <a href="manage-projects.php" class="p-4 rounded-xl bg-slate-950 hover:bg-slate-800 border border-slate-800 text-slate-300 hover:text-emerald-400 transition-all flex flex-col items-center justify-center gap-2">
                <i data-lucide="plus-circle" class="w-6 h-6 text-emerald-400"></i> Add New Project
            </a>
            <a href="manage-awards.php" class="p-4 rounded-xl bg-slate-950 hover:bg-slate-800 border border-slate-800 text-slate-300 hover:text-emerald-400 transition-all flex flex-col items-center justify-center gap-2">
                <i data-lucide="trophy" class="w-6 h-6 text-emerald-400"></i> Manage Awards
            </a>
            <a href="manage-articles.php" class="p-4 rounded-xl bg-slate-950 hover:bg-slate-800 border border-slate-800 text-slate-300 hover:text-emerald-400 transition-all flex flex-col items-center justify-center gap-2">
                <i data-lucide="edit-3" class="w-6 h-6 text-emerald-400"></i> Write New Article
            </a>
        </div>
    </div>

    <!-- Recent Messages Table -->
    <div class="bg-slate-900 rounded-3xl border border-slate-800 p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-bold text-white">Recent Messages</h2>
                <p class="text-xs text-slate-400">Submissions received via website contact form</p>
            </div>
            <a href="manage-messages.php" class="text-xs font-semibold text-emerald-400 hover:underline">View All Messages &rarr;</a>
        </div>

        <?php if (empty($recent_messages)): ?>
            <p class="text-slate-500 text-xs py-8 text-center">No contact messages received yet.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950 text-slate-400 uppercase text-[10px] font-mono border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3 px-4">Sender</th>
                            <th class="py-3 px-4">Subject</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        <?php foreach ($recent_messages as $msg): ?>
                            <tr class="hover:bg-slate-950/50">
                                <td class="py-3 px-4 font-mono text-slate-400"><?= e(date('M d, Y H:i', strtotime($msg['created_at']))) ?></td>
                                <td class="py-3 px-4 font-semibold text-white">
                                    <?= e($msg['sender_name']) ?> <span class="text-slate-500 font-normal">(&lt;<?= e($msg['sender_email']) ?>&gt;)</span>
                                </td>
                                <td class="py-3 px-4 max-w-xs truncate"><?= e($msg['subject']) ?></td>
                                <td class="py-3 px-4">
                                    <?php if ($msg['is_read'] == 0): ?>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">Unread</span>
                                    <?php else: ?>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-800 text-slate-400">Read</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <a href="manage-messages.php?view=<?= $msg['id'] ?>" class="text-emerald-400 hover:underline font-medium">Read</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/admin-footer.php'; ?>
