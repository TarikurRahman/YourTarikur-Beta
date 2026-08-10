<?php
/**
 * Global Helper Functions & Security Utilities
 */

if (!ob_get_level()) {
    ob_start();
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

/**
 * Safe HTTP Header / JavaScript Fallback Redirect Helper
 */
function redirect_to($url) {
    if (!headers_sent()) {
        header("Location: " . $url);
    } else {
        echo "<script>window.location.href='" . addslashes($url) . "';</script>";
    }
    exit;
}

function redirect($url) {
    redirect_to($url);
}

/**
 * XSS Prevention - Escapes HTML special characters safely
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Converts a string to a URL-friendly slug
 */
function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    if (empty($text)) {
        return 'n-a-' . time();
    }
    return $text;
}

/**
 * Check if Admin user is authenticated
 */
function is_admin_logged_in() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Enforce Admin authentication redirect
 */
function require_admin_login() {
    if (!is_admin_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Generate CSRF token
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Flash message toast alerts
 */
function set_flash($type, $message) {
    $_SESSION['flash_type'] = $type; // 'success', 'error', 'info'
    $_SESSION['flash_message'] = $message;
}

function get_flash() {
    if (isset($_SESSION['flash_message'])) {
        $flash = [
            'type' => $_SESSION['flash_type'] ?? 'info',
            'message' => $_SESSION['flash_message']
        ];
        unset($_SESSION['flash_type'], $_SESSION['flash_message']);
        return $flash;
    }
    return null;
}

/**
 * Secure File Upload Handler
 */
function upload_image($file_input_name, $destination_subfolder = 'uploads') {
    if (!isset($_FILES[$file_input_name]) || $_FILES[$file_input_name]['error'] !== UPLOAD_ERR_OK) {
        return null; // No file uploaded or upload error
    }

    $file = $_FILES[$file_input_name];
    $max_size = 5 * 1024 * 1024; // 5MB limit

    if ($file['size'] > $max_size) {
        throw new Exception("Uploaded image size exceeds 5MB limit.");
    }

    $allowed_mimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/svg+xml'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime_type, $allowed_mimes)) {
        throw new Exception("Invalid file format. Allowed formats: JPG, PNG, WEBP, GIF, SVG.");
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('img_', true) . '.' . strtolower($ext);

    $target_dir = __DIR__ . '/../assets/' . $destination_subfolder;
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $target_path = $target_dir . '/' . $filename;
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return 'assets/' . $destination_subfolder . '/' . $filename;
    }

    throw new Exception("Failed to move uploaded file to target directory.");
}

// ----------------------------------------------------
// FRONT-END DATA RETRIEVAL FUNCTIONS (PDO Prepared)
// ----------------------------------------------------

function get_hero_info() {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM hero_info ORDER BY id ASC LIMIT 1");
    $hero = $stmt->fetch();
    if (!$hero) {
        return [
            'status_text' => 'Available for work',
            'name' => 'Tarikur Rahman',
            'title' => 'Full Stack Developer in Bangladesh',
            'subtitle' => 'Specializing in high-performance Web Applications & Modern UI/UX',
            'pitch' => 'I craft scalable backend architectures and reactive front-end interfaces with an obsessive focus on performance.',
            'cv_url' => '#',
            'profile_image' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=600'
        ];
    }
    return $hero;
}

function get_stats() {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM stats ORDER BY display_order ASC, id ASC");
    return $stmt->fetchAll();
}

function get_services() {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM services ORDER BY display_order ASC, id ASC");
    return $stmt->fetchAll();
}

function get_projects($featured_only = false) {
    $db = getDB();
    $sql = "SELECT * FROM projects";
    if ($featured_only) {
        $sql .= " WHERE is_featured = 1";
    }
    $sql .= " ORDER BY display_order ASC, created_at DESC";
    $stmt = $db->query($sql);
    return $stmt->fetchAll();
}

function get_project_by_slug($slug) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM projects WHERE slug = :slug LIMIT 1");
    $stmt->execute([':slug' => $slug]);
    return $stmt->fetch();
}

function get_life_gallery() {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM life_gallery ORDER BY display_order ASC, id ASC");
    return $stmt->fetchAll();
}

function get_currently_building() {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM currently_building ORDER BY display_order ASC, id ASC");
    return $stmt->fetchAll();
}

function get_articles($published_only = true) {
    $db = getDB();
    $sql = "SELECT * FROM articles";
    if ($published_only) {
        $sql .= " WHERE is_published = 1";
    }
    $sql .= " ORDER BY published_at DESC, id DESC";
    $stmt = $db->query($sql);
    return $stmt->fetchAll();
}

function get_article_by_slug($slug) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM articles WHERE slug = :slug LIMIT 1");
    $stmt->execute([':slug' => $slug]);
    return $stmt->fetch();
}

function increment_article_views($id) {
    $db = getDB();
    $stmt = $db->prepare("UPDATE articles SET views = views + 1 WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

function get_awards() {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM awards ORDER BY display_order ASC, id ASC");
    return $stmt->fetchAll();
}

function get_site_settings() {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM site_settings WHERE id = 1 LIMIT 1");
    $settings = $stmt->fetch();
    if (!$settings) {
        return [
            'site_title' => 'Tarikur Rahman | Senior Full-Stack Web Developer Portfolio',
            'site_logo_text' => 'Tarikur.dev',
            'contact_email' => 'tarikur@example.com',
            'github_url' => 'https://github.com/TarikurRahman',
            'linkedin_url' => 'https://linkedin.com',
            'twitter_url' => 'https://twitter.com',
            'footer_copyright' => 'All rights reserved.'
        ];
    }
    return $settings;
}

/**
 * Fetch & Cache GitHub Contribution Heatmap & Stats (1-Hour Cache)
 */
function get_github_contributions($username = 'TarikurRahman', $token = '') {
    $username = trim($username ?: 'TarikurRahman');
    $cache_dir = __DIR__ . '/../data';
    if (!is_dir($cache_dir)) {
        @mkdir($cache_dir, 0755, true);
    }
    
    $cache_file = $cache_dir . '/github_cache_' . md5(strtolower($username)) . '.json';
    $cache_ttl = 3600; // 1 hour caching

    if (file_exists($cache_file) && (time() - filemtime($cache_file) < $cache_ttl)) {
        $cached_data = json_decode(file_get_contents($cache_file), true);
        if ($cached_data && !empty($cached_data['weeks'])) {
            return $cached_data;
        }
    }

    $days_list = [];
    $total_contributions = 0;
    $fetched_successfully = false;

    // Method 1: Official GitHub GraphQL API if Token provided
    if (!empty($token)) {
        $query = [
            'query' => 'query { user(login: "' . addslashes($username) . '") { contributionsCollection { contributionCalendar { totalContributions weeks { contributionDays { date contributionCount color } } } } } }'
        ];

        $ch = curl_init('https://api.github.com/graphql');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'PortfolioApp-cURL');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($query));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: bearer ' . trim($token)
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 8);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code === 200 && $response) {
            $json = json_decode($response, true);
            $calendar = $json['data']['user']['contributionsCollection']['contributionCalendar'] ?? null;
            if ($calendar) {
                $total_contributions = $calendar['totalContributions'] ?? 0;
                foreach ($calendar['weeks'] as $w) {
                    foreach ($w['contributionDays'] as $d) {
                        $cnt = intval($d['contributionCount']);
                        $days_list[] = [
                            'date' => $d['date'],
                            'count' => $cnt,
                            'level' => $cnt == 0 ? 0 : ($cnt <= 2 ? 1 : ($cnt <= 4 ? 2 : ($cnt <= 6 ? 3 : 4)))
                        ];
                    }
                }
                $fetched_successfully = true;
            }
        }
    }

    // Method 2: Public GitHub HTML scraping fallback if no token
    if (!$fetched_successfully) {
        $ch = curl_init("https://github.com/users/" . urlencode($username) . "/contributions");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 8);
        $html = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code === 200 && $html) {
            preg_match_all('/<rect[^>]*data-date="([^"]+)"[^>]*data-count="(\d+)"/i', $html, $rect_matches, PREG_SET_ORDER);
            if (empty($rect_matches)) {
                preg_match_all('/<td[^>]*data-date="([^"]+)"[^>]*data-level="(\d+)"/i', $html, $td_matches, PREG_SET_ORDER);
                foreach ($td_matches as $m) {
                    $lvl = intval($m[2]);
                    $cnt = $lvl * 2;
                    $total_contributions += $cnt;
                    $days_list[] = [
                        'date' => $m[1],
                        'count' => $cnt,
                        'level' => $lvl
                    ];
                }
            } else {
                foreach ($rect_matches as $m) {
                    $cnt = intval($m[2]);
                    $total_contributions += $cnt;
                    $days_list[] = [
                        'date' => $m[1],
                        'count' => $cnt,
                        'level' => $cnt == 0 ? 0 : ($cnt <= 2 ? 1 : ($cnt <= 4 ? 2 : ($cnt <= 6 ? 3 : 4)))
                    ];
                }
            }

            if (count($days_list) > 30) {
                $fetched_successfully = true;
            }
        }
    }

    // Fallback pseudo-generator if offline or user has 0 fetched days
    if (!$fetched_successfully || count($days_list) < 30) {
        $days_list = [];
        $total_contributions = 0;
        $start_date = new DateTime();
        $start_date->modify('-364 days');

        for ($i = 0; $i < 365; $i++) {
            $cur_date = clone $start_date;
            $cur_date->modify("+$i days");
            $date_str = $cur_date->format('Y-m-d');
            $rand = (crc32($username . $date_str) % 100) / 100;
            $cnt = 0;
            $lvl = 0;
            if ($rand > 0.45) {
                $cnt = (crc32($date_str) % 9) + 1;
                $lvl = $cnt <= 2 ? 1 : ($cnt <= 4 ? 2 : ($cnt <= 6 ? 3 : 4));
            }
            $total_contributions += $cnt;
            $days_list[] = [
                'date' => $date_str,
                'count' => $cnt,
                'level' => $lvl
            ];
        }
    }

    // Calculate streaks
    $current_streak = 0;
    $max_streak = 0;
    $temp_streak = 0;

    usort($days_list, function($a, $b) { return strcmp($a['date'], $b['date']); });

    foreach ($days_list as $day) {
        if ($day['count'] > 0) {
            $temp_streak++;
            if ($temp_streak > $max_streak) $max_streak = $temp_streak;
        } else {
            $temp_streak = 0;
        }
    }

    for ($i = count($days_list) - 1; $i >= 0; $i--) {
        if ($days_list[$i]['count'] > 0) {
            $current_streak++;
        } else {
            if ($i === count($days_list) - 1) continue;
            break;
        }
    }

    $weeks = array_chunk($days_list, 7);

    $result = [
        'username' => $username,
        'total_contributions' => $total_contributions,
        'current_streak' => max(1, $current_streak),
        'max_streak' => max(1, $max_streak),
        'weeks' => $weeks,
        'updated_at' => date('Y-m-d H:i:s')
    ];

    @file_put_contents($cache_file, json_encode($result, JSON_PRETTY_PRINT));
    return $result;
}
