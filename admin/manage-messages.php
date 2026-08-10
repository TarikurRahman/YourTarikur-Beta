<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin_login();

$db = getDB();

$view_id = intval($_GET['view'] ?? 0);
$delete_id = intval($_GET['delete'] ?? 0);

if ($delete_id > 0) {
    if (verify_csrf_token($_GET['token'] ?? '')) {
        $stmt = $db->prepare("DELETE FROM messages WHERE id = :id");
        $stmt->execute([':id' => $delete_id]);
        set_flash('success', 'Message deleted from inbox.');
    }
    redirect('manage-messages.php');
}

// Include Admin Header layout AFTER handling actions
require_once __DIR__ . '/admin-header.php';

$csrf_token = generate_csrf_token();

$view_message = null;
if ($view_id > 0) {
    $stmt = $db->prepare("SELECT * FROM messages WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $view_id]);
    $view_message = $stmt->fetch();

    if ($view_message && $view_message['is_read'] == 0) {
        $stmt_read = $db->prepare("UPDATE messages SET is_read = 1 WHERE id = :id");
        $stmt_read->execute([':id' => $view_id]);
    }
}

$all_messages = $db->query("SELECT * FROM messages ORDER BY created_at DESC")->fetchAll();
?>

<div class="space-y-8">
    <div>
        <h1 class="text-2xl font-extrabold text-white">Contact Inbox Messages</h1>
        <p class="text-xs text-slate-400">View and respond to client inquiries sent via the website contact form</p>
    </div>

    <?php if ($view_message): ?>
        <div class="bg-slate-900 p-8 rounded-3xl border border-slate-800 shadow-xl space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                <div>
                    <span class="text-xs font-mono text-slate-400"><?= e(date('F j, Y - H:i', strtotime($view_message['created_at']))) ?></span>
                    <h2 class="text-xl font-bold text-white mt-1"><?= e($view_message['subject']) ?></h2>
                    <div class="text-xs text-slate-300 mt-1">
                        From: <strong class="text-emerald-400"><?= e($view_message['sender_name']) ?></strong> 
                        (&lt;<a href="mailto:<?= e($view_message['sender_email']) ?>" class="underline hover:text-white"><?= e($view_message['sender_email']) ?></a>&gt;)
                    </div>
                </div>
                <a href="manage-messages.php" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-800 text-slate-300 hover:bg-slate-700">
                    &larr; Back to Inbox
                </a>
            </div>

            <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800 text-slate-300 text-sm whitespace-pre-line leading-relaxed">
                <?= e($view_message['message']) ?>
            </div>

            <div class="flex items-center justify-between pt-4">
                <a href="mailto:<?= e($view_message['sender_email']) ?>?subject=Re: <?= urlencode($view_message['subject']) ?>" class="px-6 py-2.5 rounded-xl text-xs font-bold bg-emerald-500 hover:bg-emerald-400 text-slate-950 flex items-center gap-2">
                    <i data-lucide="mail-reply" class="w-4 h-4"></i> Reply via Email
                </a>
                <a href="manage-messages.php?delete=<?= $view_message['id'] ?>&token=<?= $csrf_token ?>" onclick="return confirm('Delete this message?')" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-rose-400 hover:underline">
                    Delete Message
                </a>
            </div>
        </div>
    <?php endif; ?>

    <div class="bg-slate-900 rounded-3xl border border-slate-800 overflow-hidden shadow-xl">
        <div class="p-6 border-b border-slate-800 flex items-center justify-between">
            <h2 class="text-lg font-bold text-white">All Messages (<?= count($all_messages) ?>)</h2>
        </div>

        <?php if (empty($all_messages)): ?>
            <p class="text-slate-500 text-xs py-12 text-center">Your contact inbox is empty.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950 text-slate-400 uppercase text-[10px] font-mono border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3 px-4">Sender</th>
                            <th class="py-3 px-4">Subject</th>
                            <th class="py-3 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        <?php foreach ($all_messages as $m): ?>
                            <tr class="hover:bg-slate-950/50 <?= $m['is_read'] == 0 ? 'bg-slate-950/30 font-semibold' : '' ?>">
                                <td class="py-3 px-4">
                                    <?php if ($m['is_read'] == 0): ?>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">New</span>
                                    <?php else: ?>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-800 text-slate-500">Read</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-4 font-mono text-slate-400"><?= e(date('M d, Y H:i', strtotime($m['created_at']))) ?></td>
                                <td class="py-3 px-4 text-white">
                                    <?= e($m['sender_name']) ?>
                                    <div class="text-[10px] text-slate-500 font-mono"><?= e($m['sender_email']) ?></div>
                                </td>
                                <td class="py-3 px-4 max-w-xs truncate"><?= e($m['subject']) ?></td>
                                <td class="py-3 px-4 text-right space-x-2">
                                    <a href="manage-messages.php?view=<?= $m['id'] ?>" class="text-emerald-400 hover:underline font-semibold">View</a>
                                    <a href="manage-messages.php?delete=<?= $m['id'] ?>&token=<?= $csrf_token ?>" onclick="return confirm('Delete message?')" class="text-rose-400 hover:underline font-semibold">Delete</a>
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
