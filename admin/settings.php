<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin_login();

$db = getDB();
$admin_id = $_SESSION['admin_id'] ?? 1;

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Invalid security token.');
        redirect_to('settings.php');
    }

    $form_type = $_POST['form_type'] ?? 'admin';

    if ($form_type === 'site_config') {
        $site_title = trim($_POST['site_title'] ?? '');
        $site_logo_text = trim($_POST['site_logo_text'] ?? '');
        $contact_email = trim($_POST['contact_email'] ?? '');
        $github_url = trim($_POST['github_url'] ?? '');
        $linkedin_url = trim($_POST['linkedin_url'] ?? '');
        $facebook_url = trim($_POST['facebook_url'] ?? '');
        $instagram_url = trim($_POST['instagram_url'] ?? '');
        $tiktok_url = trim($_POST['tiktok_url'] ?? '');
        $twitter_url = trim($_POST['twitter_url'] ?? '');
        $website_url = trim($_POST['website_url'] ?? '');
        $footer_copyright = trim($_POST['footer_copyright'] ?? '');
        $ai_chatbot_enabled = isset($_POST['ai_chatbot_enabled']) ? 1 : 0;
        $ai_api_key = trim($_POST['ai_api_key'] ?? '');

        try {
            $stmt = $db->prepare("UPDATE site_settings SET 
                site_title = :site_title,
                site_logo_text = :site_logo_text,
                contact_email = :contact_email,
                github_url = :github_url,
                linkedin_url = :linkedin_url,
                facebook_url = :facebook_url,
                instagram_url = :instagram_url,
                tiktok_url = :tiktok_url,
                twitter_url = :twitter_url,
                website_url = :website_url,
                footer_copyright = :footer_copyright,
                ai_chatbot_enabled = :ai_chatbot_enabled,
                ai_api_key = :ai_api_key
                WHERE id = 1");

            $stmt->execute([
                ':site_title' => $site_title,
                ':site_logo_text' => $site_logo_text,
                ':contact_email' => $contact_email,
                ':github_url' => $github_url,
                ':linkedin_url' => $linkedin_url,
                ':facebook_url' => $facebook_url,
                ':instagram_url' => $instagram_url,
                ':tiktok_url' => $tiktok_url,
                ':twitter_url' => $twitter_url,
                ':website_url' => $website_url,
                ':footer_copyright' => $footer_copyright,
                ':ai_chatbot_enabled' => $ai_chatbot_enabled,
                ':ai_api_key' => $ai_api_key
            ]);

            set_flash('success', 'Website configuration & social media profiles updated successfully!');
        } catch (Exception $e) {
            set_flash('error', 'Failed to update website settings: ' . $e->getMessage());
        }
        redirect_to('settings.php');
    }

    if ($form_type === 'admin') {
        $stmt_adm = $db->prepare("SELECT * FROM admin_users WHERE id = :id LIMIT 1");
        $stmt_adm->execute([':id' => $admin_id]);
        $admin_cur = $stmt_adm->fetch();

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (empty($name) || empty($email)) {
            set_flash('error', 'Name and email are required.');
        } else {
            try {
                if (!empty($new_password)) {
                    if (!password_verify($current_password, $admin_cur['password_hash'])) {
                        set_flash('error', 'Current password entered is incorrect.');
                        redirect_to('settings.php');
                    }
                    if ($new_password !== $confirm_password) {
                        set_flash('error', 'New password and confirmation do not match.');
                        redirect_to('settings.php');
                    }
                    if (strlen($new_password) < 6) {
                        set_flash('error', 'New password must be at least 6 characters long.');
                        redirect_to('settings.php');
                    }

                    $new_hash = password_hash($new_password, PASSWORD_BCRYPT);
                    $stmt_up = $db->prepare("UPDATE admin_users SET name = :name, email = :email, password_hash = :hash WHERE id = :id");
                    $stmt_up->execute([':name' => $name, ':email' => $email, ':hash' => $new_hash, ':id' => $admin_id]);
                } else {
                    $stmt_up = $db->prepare("UPDATE admin_users SET name = :name, email = :email WHERE id = :id");
                    $stmt_up->execute([':name' => $name, ':email' => $email, ':id' => $admin_id]);
                }

                $_SESSION['admin_name'] = $name;
                set_flash('success', 'Admin security settings updated successfully.');
            } catch (Exception $e) {
                set_flash('error', 'Error updating admin credentials: ' . $e->getMessage());
            }
        }
        redirect_to('settings.php');
    }
}

// Include Admin Header layout AFTER request handling
require_once __DIR__ . '/admin-header.php';

$csrf_token = generate_csrf_token();
$site_settings = get_site_settings();

$stmt = $db->prepare("SELECT * FROM admin_users WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $admin_id]);
$admin = $stmt->fetch();
?>

<div class="max-w-4xl mx-auto space-y-10">
    <div>
        <h1 class="text-2xl font-extrabold text-white font-heading">Website & Admin Settings</h1>
        <p class="text-xs text-slate-400 mt-1">Manage global site configuration, social media URLs, branding, and administrator security.</p>
    </div>

    <!-- 1. Global Website & Branding Settings -->
    <div class="bg-slate-900 p-8 rounded-3xl border border-slate-800 shadow-xl space-y-6">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex items-center justify-center">
                <i data-lucide="globe" class="w-5 h-5"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-white">Global Website & Branding</h2>
                <p class="text-xs text-slate-400">Site title, logo brand text, contact email, and social media links</p>
            </div>
        </div>

        <form method="POST" action="settings.php" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= e($csrf_token) ?>">
            <input type="hidden" name="form_type" value="site_config">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="site_title" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Browser Meta Title</label>
                    <input type="text" id="site_title" name="site_title" value="<?= e($site_settings['site_title'] ?? '') ?>" required class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500">
                </div>
                <div>
                    <label for="site_logo_text" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Header Logo Text</label>
                    <input type="text" id="site_logo_text" name="site_logo_text" value="<?= e($site_settings['site_logo_text'] ?? 'Tarikur.dev') ?>" required class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500 font-mono">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="contact_email" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Public Contact Email</label>
                    <input type="email" id="contact_email" name="contact_email" value="<?= e($site_settings['contact_email'] ?? '') ?>" required class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500">
                </div>
                <div>
                    <label for="footer_copyright" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Footer Copyright Text</label>
                    <input type="text" id="footer_copyright" name="footer_copyright" value="<?= e($site_settings['footer_copyright'] ?? 'All rights reserved.') ?>" required class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-800 space-y-4">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i data-lucide="share-2" class="w-4 h-4 text-emerald-400"></i> Social Media & Web Profiles
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <label for="github_url" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">GitHub Profile URL</label>
                        <input type="url" id="github_url" name="github_url" value="<?= e($site_settings['github_url'] ?? 'https://github.com/tarikurrahman') ?>" placeholder="https://github.com/tarikurrahman" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500 font-mono">
                    </div>

                    <div>
                        <label for="linkedin_url" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">LinkedIn Profile URL</label>
                        <input type="url" id="linkedin_url" name="linkedin_url" value="<?= e($site_settings['linkedin_url'] ?? 'https://www.linkedin.com/in/tarikurrahman08') ?>" placeholder="https://www.linkedin.com/in/tarikurrahman08" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500 font-mono">
                    </div>

                    <div>
                        <label for="facebook_url" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Facebook Profile URL</label>
                        <input type="url" id="facebook_url" name="facebook_url" value="<?= e($site_settings['facebook_url'] ?? 'https://www.facebook.com/tarikurrahman08') ?>" placeholder="https://www.facebook.com/tarikurrahman08" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500 font-mono">
                    </div>

                    <div>
                        <label for="instagram_url" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Instagram Profile URL</label>
                        <input type="url" id="instagram_url" name="instagram_url" value="<?= e($site_settings['instagram_url'] ?? 'https://www.instagram.com/tarikurrahman08') ?>" placeholder="https://www.instagram.com/tarikurrahman08" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500 font-mono">
                    </div>

                    <div>
                        <label for="tiktok_url" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">TikTok Profile URL</label>
                        <input type="url" id="tiktok_url" name="tiktok_url" value="<?= e($site_settings['tiktok_url'] ?? 'https://www.tiktok.com/@tarikurrahman.bd') ?>" placeholder="https://www.tiktok.com/@tarikurrahman.bd" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500 font-mono">
                    </div>

                    <div>
                        <label for="twitter_url" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Twitter / X URL</label>
                        <input type="url" id="twitter_url" name="twitter_url" value="<?= e($site_settings['twitter_url'] ?? 'https://x.com/tarikurrahman08') ?>" placeholder="https://x.com/tarikurrahman08" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500 font-mono">
                    </div>

                    <div class="md:col-span-2 lg:col-span-3">
                        <label for="website_url" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Portfolio Web App URL (Vercel / Live)</label>
                        <input type="url" id="website_url" name="website_url" value="<?= e($site_settings['website_url'] ?? 'https://yourtarikur.vercel.app') ?>" placeholder="https://yourtarikur.vercel.app" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500 font-mono">
                    </div>
                </div>
            </div>

            <!-- AI Chatbot Settings -->
            <div class="pt-6 border-t border-slate-800 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-white flex items-center gap-2">
                            <i data-lucide="bot" class="w-4 h-4 text-emerald-400"></i> AI Portfolio Assistant Widget
                        </h3>
                        <p class="text-xs text-slate-400">Enable floating AI chatbot widget and configure API credentials</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="ai_chatbot_enabled" value="1" <?= ($site_settings['ai_chatbot_enabled'] ?? 1) ? 'checked' : '' ?> class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-950 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                    </label>
                </div>

                <div>
                    <label for="ai_api_key" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">OpenAI / Gemini API Key (Optional)</label>
                    <input type="password" id="ai_api_key" name="ai_api_key" value="<?= e($site_settings['ai_api_key'] ?? '') ?>" placeholder="sk-... or AIzaSy... (Leave blank for Smart Local Fallback)" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500 font-mono">
                    <p class="text-[11px] text-slate-500 mt-1.5">If left blank, the chatbot operates smoothly using Tarikur's built-in intelligent portfolio knowledge base!</p>
                </div>
            </div>

            <button type="submit" class="px-6 py-3 rounded-xl text-xs font-bold uppercase tracking-wider bg-emerald-500 text-slate-950 hover:bg-emerald-400 transition-all shadow-lg shadow-emerald-500/20">
                Save Site Settings
            </button>
        </form>
    </div>

    <!-- 2. Admin Security Credentials Settings -->
    <div class="bg-slate-900 p-8 rounded-3xl border border-slate-800 shadow-xl space-y-6">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-teal-500/10 text-teal-400 border border-teal-500/20 flex items-center justify-center">
                <i data-lucide="shield-check" class="w-5 h-5"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-white">Administrator Security & Password</h2>
                <p class="text-xs text-slate-400">Update login credentials and security password</p>
            </div>
        </div>

        <form method="POST" action="settings.php" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= e($csrf_token) ?>">
            <input type="hidden" name="form_type" value="admin">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Display Name</label>
                    <input type="text" id="name" name="name" value="<?= e($admin['name'] ?? '') ?>" required class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500">
                </div>
                <div>
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Admin Email</label>
                    <input type="email" id="email" name="email" value="<?= e($admin['email'] ?? '') ?>" required class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500">
                </div>
            </div>

            <hr class="border-slate-800">

            <div>
                <h3 class="text-sm font-bold text-white mb-1">Change Password</h3>
                <p class="text-xs text-slate-400 mb-4">Leave blank if you do not wish to change your password.</p>

                <div class="space-y-4">
                    <div>
                        <label for="current_password" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Current Password</label>
                        <input type="password" id="current_password" name="current_password" placeholder="••••••••" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="new_password" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">New Password</label>
                            <input type="password" id="new_password" name="new_password" placeholder="••••••••" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500">
                        </div>
                        <div>
                            <label for="confirm_password" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" placeholder="••••••••" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:border-emerald-500">
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="px-6 py-3 rounded-xl text-xs font-bold uppercase tracking-wider bg-teal-500 text-slate-950 hover:bg-teal-400 transition-all shadow-lg shadow-teal-500/20">
                Update Security Credentials
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/admin-footer.php'; ?>
