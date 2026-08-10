<?php
/**
 * Database Configuration & Connection Handler
 * Uses PDO for secure prepared statement queries against MySQL or SQLite fallback.
 * Supports Vercel Serverless Environment Variables (getenv / $_ENV).
 */

$db_host = getenv('DB_HOST') ?: (getenv('MYSQLHOST') ?: 'localhost');
$db_port = getenv('DB_PORT') ?: (getenv('MYSQLPORT') ?: '3306');
$db_name = getenv('DB_NAME') ?: (getenv('MYSQLDATABASE') ?: 'portfolio_db');
$db_user = getenv('DB_USER') ?: (getenv('MYSQLUSER') ?: 'root');
$db_pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : (getenv('MYSQLPASSWORD') !== false ? getenv('MYSQLPASSWORD') : '');

if (!defined('DB_HOST')) define('DB_HOST', $db_host);
if (!defined('DB_PORT')) define('DB_PORT', $db_port);
if (!defined('DB_NAME')) define('DB_NAME', $db_name);
if (!defined('DB_USER')) define('DB_USER', $db_user);
if (!defined('DB_PASS')) define('DB_PASS', $db_pass);

$pdo = null;

try {
    // Attempt MySQL Connection first
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    // SSL Attributes for Cloud MySQL (TiDB Cloud, Aiven, PlanetScale, AWS RDS)
    if (defined('PDO::MYSQL_ATTR_SSL_CA')) {
        $options[PDO::MYSQL_ATTR_SSL_CA] = true;
    }
    if (defined('PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT')) {
        $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
    }

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // Fallback to SQLite if MySQL connection fails or database does not exist
    try {
        // On Vercel Serverless, use /tmp directory for writable SQLite instance
        $is_vercel = getenv('VERCEL') || getenv('AWS_LAMBDA_FUNCTION_NAME');
        $sqlite_dir = $is_vercel ? '/tmp' : __DIR__ . '/../data';

        if (!is_dir($sqlite_dir)) {
            @mkdir($sqlite_dir, 0755, true);
        }
        $sqlite_path = $sqlite_dir . '/portfolio.sqlite';
        $pdo = new PDO("sqlite:" . $sqlite_path);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // Ensure tables exist in SQLite database
        init_sqlite_tables($pdo);
    } catch (PDOException $ex) {
        die("Database Connection Error: " . $ex->getMessage());
    }
}

/**
 * Initialize SQLite database structure and initial seed data if empty
 */
function init_sqlite_tables($pdo) {
    $queries = [
        "CREATE TABLE IF NOT EXISTS admin_users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            email TEXT NOT NULL,
            name TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );",
        "CREATE TABLE IF NOT EXISTS hero_info (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            status_text TEXT DEFAULT 'Available for work',
            name TEXT NOT NULL,
            title TEXT NOT NULL,
            subtitle TEXT,
            pitch TEXT,
            cv_url TEXT DEFAULT '#',
            profile_image TEXT DEFAULT 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=600',
            github_username TEXT DEFAULT 'TarikurRahman',
            github_token TEXT DEFAULT '',
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );",
        "CREATE TABLE IF NOT EXISTS stats (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            stat_key TEXT NOT NULL UNIQUE,
            stat_value TEXT NOT NULL,
            stat_label TEXT NOT NULL,
            icon TEXT DEFAULT 'award',
            display_order INTEGER DEFAULT 0
        );",
        "CREATE TABLE IF NOT EXISTS life_gallery (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            caption TEXT,
            image_url TEXT NOT NULL,
            location TEXT DEFAULT '',
            display_order INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );",
        "CREATE TABLE IF NOT EXISTS projects (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            slug TEXT NOT NULL UNIQUE,
            description TEXT NOT NULL,
            full_description TEXT,
            image_url TEXT NOT NULL,
            tech_stack TEXT NOT NULL,
            live_url TEXT DEFAULT '#',
            github_url TEXT DEFAULT '#',
            is_featured INTEGER DEFAULT 1,
            display_order INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );",
        "CREATE TABLE IF NOT EXISTS services (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            description TEXT NOT NULL,
            icon TEXT DEFAULT 'code',
            display_order INTEGER DEFAULT 0
        );",
        "CREATE TABLE IF NOT EXISTS currently_building (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            description TEXT NOT NULL,
            status TEXT DEFAULT 'In Progress',
            progress_percent INTEGER DEFAULT 50,
            tech_stack TEXT DEFAULT '',
            icon TEXT DEFAULT 'terminal',
            display_order INTEGER DEFAULT 0
        );",
        "CREATE TABLE IF NOT EXISTS articles (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            slug TEXT NOT NULL UNIQUE,
            excerpt TEXT NOT NULL,
            content TEXT NOT NULL,
            thumbnail TEXT DEFAULT 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?auto=format&fit=crop&q=80&w=800',
            read_time TEXT DEFAULT '5 min read',
            published_at DATE NOT NULL,
            is_published INTEGER DEFAULT 1,
            views INTEGER DEFAULT 0
        );",
        "CREATE TABLE IF NOT EXISTS messages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            sender_name TEXT NOT NULL,
            sender_email TEXT NOT NULL,
            subject TEXT NOT NULL,
            message TEXT NOT NULL,
            is_read INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );",
        "CREATE TABLE IF NOT EXISTS awards (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            category TEXT NOT NULL,
            title TEXT NOT NULL,
            team_name TEXT DEFAULT '',
            institution TEXT DEFAULT '',
            event_date TEXT DEFAULT '',
            location TEXT DEFAULT '',
            organizer TEXT DEFAULT '',
            description TEXT,
            display_order INTEGER DEFAULT 0
        );",
        "CREATE TABLE IF NOT EXISTS site_settings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            site_title TEXT NOT NULL,
            site_logo_text TEXT NOT NULL,
            contact_email TEXT NOT NULL,
            github_url TEXT DEFAULT 'https://github.com/tarikurrahman',
            linkedin_url TEXT DEFAULT 'https://www.linkedin.com/in/tarikurrahman08',
            facebook_url TEXT DEFAULT 'https://www.facebook.com/tarikurrahman08',
            instagram_url TEXT DEFAULT 'https://www.instagram.com/tarikurrahman08',
            tiktok_url TEXT DEFAULT 'https://www.tiktok.com/@tarikurrahman.bd',
            twitter_url TEXT DEFAULT 'https://x.com/tarikurrahman08',
            website_url TEXT DEFAULT 'https://yourtarikur.vercel.app',
            footer_copyright TEXT DEFAULT '',
            ai_chatbot_enabled INTEGER DEFAULT 1,
            ai_api_key TEXT DEFAULT ''
        );"
    ];

    foreach ($queries as $q) {
        $pdo->exec($q);
    }

    // Auto-migrate new columns if existing database table
    try { @$pdo->exec("ALTER TABLE hero_info ADD COLUMN github_username TEXT DEFAULT 'tarikurrahman'"); } catch (Exception $e) {}
    try { @$pdo->exec("ALTER TABLE hero_info ADD COLUMN github_token TEXT DEFAULT ''"); } catch (Exception $e) {}

    // Seed Admin User if not present
    $stmt = $pdo->query("SELECT COUNT(*) FROM admin_users");
    if ($stmt->fetchColumn() == 0) {
        // admin / admin123
        $pdo->exec("INSERT INTO admin_users (username, password_hash, email, name) VALUES 
            ('admin', '" . password_hash('admin123', PASSWORD_BCRYPT) . "', 'admin@example.com', 'Tarikur Rahman')");
    }

    // Seed Hero Info
    $stmt = $pdo->query("SELECT COUNT(*) FROM hero_info");
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO hero_info (status_text, name, title, subtitle, pitch, cv_url, profile_image) VALUES 
            ('Available for work', 
             'Tarikur Rahman', 
             'Robotics Inventor & Tech Researcher', 
             'Specializing in high-performance Web Applications, Native PHP, Node.js, and Modern UI/UX Architecture.',
             'I craft scalable backend architectures, reactive front-end interfaces, and robust REST APIs with an obsessive focus on performance, clean code, and elegant user experience.',
             '#',
             'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=600')");
    }

    // Seed Stats
    $stmt = $pdo->query("SELECT COUNT(*) FROM stats");
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO stats (stat_key, stat_value, stat_label, icon, display_order) VALUES 
            ('exp', '2+', 'Years Experience', 'calendar', 1),
            ('projects', '25+', 'Projects Completed', 'folder-git-2', 2),
            ('clients', '15+', 'Happy Clients', 'users', 3),
            ('contributions', '100+', 'Open-Source Contributions', 'git-commit', 4)");
    }

    // Seed Services
    $stmt = $pdo->query("SELECT COUNT(*) FROM services");
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO services (title, description, icon, display_order) VALUES 
            ('Full Stack Web Development', 'End-to-end web apps built with Native PHP, MySQL, React, and modern CSS frameworks optimized for lightning load speeds.', 'code-2', 1),
            ('RESTful API & Backend Systems', 'Architecting secure, well-documented API services, authentication pipelines, and database optimizations with PDO.', 'server', 2),
            ('AI & Automation Integration', 'Embedding OpenAI / LLM features, AI vision processing, and automated workflow integrations directly into web dashboards.', 'bot', 3),
            ('Performance & Security Audit', 'Optimizing database queries, caching strategies, audit logging, SQLi/XSS vulnerability fixes, and Lighthouse 95+ scores.', 'zap', 4)");
    }

    // Seed Projects
    $stmt = $pdo->query("SELECT COUNT(*) FROM projects");
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO projects (title, slug, description, full_description, image_url, tech_stack, live_url, github_url, is_featured, display_order) VALUES 
            ('Antigravity Cloud IDE Portal', 'antigravity-cloud-ide-portal', 
             'A real-time developer environment dashboard with integrated terminal, agent task manager, and code telemetry analytics.',
             'Antigravity Cloud IDE Portal is an enterprise-level SaaS dashboard designed for remote engineering teams. Built with Native PHP 8, MySQL PDO, Tailwind CSS, and WebSockets. Features role-based access control, file tree management, live execution logs, and automated code review pipelines.',
             'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&q=80&w=800', 
             'PHP 8, MySQL, PDO, Tailwind CSS, JavaScript', '#', 'https://github.com/tarikurrahman', 1, 1),
            ('EcoMetrics SaaS Analytics', 'ecometrics-saas-analytics', 
             'High-throughput telemetry dashboard tracking carbon offset metrics and API resource consumption for cloud workloads.',
             'EcoMetrics aggregates microservice logs and infrastructure metrics across multi-cloud clusters into actionable charts. Includes custom reporting, PDF export, scheduled cron jobs, and granular REST endpoint monitoring.',
             'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&q=80&w=800', 
             'PHP, MySQL, Chart.js, REST API, Tailwind', '#', 'https://github.com/tarikurrahman', 1, 2),
            ('NexGen E-Commerce Platform', 'nexgen-e-commerce-platform', 
             'Ultra-fast headless online shop engine with inventory sync, automated PDF invoices, and multi-currency payment gateways.',
             'A full-featured e-commerce engine equipped with dynamic product variant matrix, Stripe & SSLCommerz checkout hooks, session cart, promo code engine, and a comprehensive admin backend panel.',
             'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&q=80&w=800', 
             'Native PHP, PDO, MySQL, Alpine.js, Tailwind', '#', 'https://github.com/tarikurrahman', 1, 3)");
    }

    // Seed Gallery
    $stmt = $pdo->query("SELECT COUNT(*) FROM life_gallery");
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO life_gallery (title, caption, image_url, location, display_order) VALUES 
            ('Sylhet Tea Gardens Trek', 'Exploring the lush green hills and misty trails during a weekend recharge getaway.', 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&q=80&w=600', 'Sylhet, Bangladesh', 1),
            ('Late Night Code & Coffee', 'Debugging complex asynchronous algorithms at 2 AM with dark roast coffee.', 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&q=80&w=600', 'Home Studio', 2),
            ('Tech Meetup Keynote', 'Sharing best practices on PHP PDO optimization and clean backend architecture.', 'https://images.unsplash.com/photo-1475721027785-f74eccf877e2?auto=format&fit=crop&q=80&w=600', 'Dhaka Tech Hub', 3),
            ('Mountain Summit Sunset', 'Reaching the peak after a 5-hour hike — nature is the ultimate inspiration.', 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&q=80&w=600', 'Bandarban Heights', 4)");
    }

    // Seed Currently Building
    $stmt = $pdo->query("SELECT COUNT(*) FROM currently_building");
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO currently_building (title, description, status, progress_percent, tech_stack, icon, display_order) VALUES 
            ('Autonomous AI Workflow Agents', 'Building custom agentic workflows that integrate LLM tool-calling with PHP CLI runners.', 'Active Build', 85, 'PHP 8.2, OpenAI API, cURL', 'bot', 1),
            ('Distributed Caching Layer', 'Designing an in-memory key-value cache wrapper for high-frequency PDO query results.', 'Research & Dev', 65, 'PHP, Redis, PDO', 'database', 2),
            ('Next.js App Router Masterclass', 'Mastering server components, streaming SSR, and edge middleware optimization.', 'Continuous Learning', 90, 'Next.js 14, React, TypeScript', 'sparkles', 3)");
    }

    // Seed Articles
    $stmt = $pdo->query("SELECT COUNT(*) FROM articles");
    if ($stmt->fetchColumn() == 0) {
        $insert_art = $pdo->prepare("INSERT INTO articles (title, slug, excerpt, content, thumbnail, read_time, published_at, is_published, views) VALUES (:title, :slug, :excerpt, :content, :thumbnail, :read_time, :published_at, :is_published, :views)");
        
        $insert_art->execute([
            ':title' => 'Mastering PHP PDO: Security, Prepared Statements, and Performance Optimization',
            ':slug' => 'mastering-php-pdo-security-prepared-statements-performance',
            ':excerpt' => 'Learn how to write rock-solid database access layers in Native PHP using PDO prepared statements to completely eliminate SQL injection threats while maximizing query execution speed.',
            ':content' => "PHP Data Objects (PDO) provides a lightweight, consistent interface for accessing databases in PHP. Unlike legacy mysql_* functions or standard mysqli, PDO supports multi-database drivers and prepared statements natively.\n\n### Why PDO Prepared Statements Matter\nPrepared statements separate SQL code from user-supplied data. When executing PDO prepared statements, the database compiles the SQL query structure first. When you execute it with bound parameters, user data is treated strictly as literal values — never as executable SQL commands.\n\n### Best Practices for PDO Security\n1. Always set PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION to catch connection & query errors gracefully.\n2. Turn off native emulation with PDO::ATTR_EMULATE_PREPARES => false to enforce true server-side prepared statements.\n3. Always fetch associative arrays with PDO::FETCH_ASSOC to keep payload structures clean.\n\nBy following these principles, your native PHP applications remain fast, robust, and completely secure against SQL injection attacks.",
            ':thumbnail' => 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?auto=format&fit=crop&q=80&w=800',
            ':read_time' => '6 min read',
            ':published_at' => '2026-08-01',
            ':is_published' => 1,
            ':views' => 142
        ]);

        $insert_art->execute([
            ':title' => 'Building Dark/Light Theme Switching with Tailwind CSS and LocalStorage',
            ':slug' => 'building-dark-light-theme-switching-tailwind-css-localstorage',
            ':excerpt' => 'A step-by-step guide to crafting smooth dark mode toggles with zero layout flash using CSS variables, Tailwind classes, and browser LocalStorage persistence.',
            ':content' => "Dark mode has transitioned from a trendy feature to an essential accessibility and UI expectation. Implementing a flawless theme toggle requires managing three key aspects: OS color scheme preference (prefers-color-scheme), manual toggle state in localStorage, and avoiding screen flash during page load.\n\n```js\n// Inline theme initialization before DOM renders to prevent flicker\nif (localStorage.theme === \"dark\" || (!(\"theme\" in localStorage) && window.matchMedia(\"(prefers-color-scheme: dark)\").matches)) {\n  document.documentElement.classList.add(\"dark\");\n} else {\n  document.documentElement.classList.remove(\"dark\");\n}\n```\n\nCombine this script with Tailwind's darkMode: \"class\" configuration and CSS transition variables for a seamless, ultra-modern portfolio visual experience.",
            ':thumbnail' => 'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?auto=format&fit=crop&q=80&w=800',
            ':read_time' => '4 min read',
            ':published_at' => '2026-08-05',
            ':is_published' => 1,
            ':views' => 98
        ]);
    }

    // Seed Awards & Achievements
    $stmt = $pdo->query("SELECT COUNT(*) FROM awards");
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO awards (category, title, team_name, institution, event_date, location, organizer, description, display_order) VALUES 
            ('National & International', 
             'Gold Medalist - 8th World Invention Competition and Exhibition (WICE) 2026', 
             'Team DEMON71', 
             'Alif Subhan Chowdhury Government College', 
             '9 May 2026', 
             'Northern University Bangladesh', 
             'IYSA & Dreams of Bangladesh', 
             'Awarded Gold Medal in IoT & Robotics category for developing autonomous hardware-software automation and smart robotics systems.', 
             1),
            ('National & International', 
             'Special 5th Place - 47th National Science & Technology Week 2026', 
             'Team DEMON71', 
             'Alif Subhan Chowdhury Government College', 
             'June 2026', 
             'Agargaon, Dhaka', 
             'National Museum of Science & Technology (NMST)', 
             'Recognized nationally among top young innovators across Bangladesh for smart robotics and embedded systems engineering.', 
             2),
            ('National & International', 
             'Top 10 Finalist - National Science Fest 2025', 
             'Team DEMON71', 
             'Alif Subhan Chowdhury Government College', 
             '2025', 
             'Dhaka, Bangladesh', 
             'Bangladesh Science Club', 
             'Selected as a Top 10 Finalist for designing and deploying an Autonomous Home Automation & Fire Fighting Robot.', 
             3),
            ('Divisional & District', 
             '1st Place (Divisional Champion) - 47th National Science & Technology Fair 2026', 
             'Team DEMON71', 
             'Alif Subhan Chowdhury Government College', 
             '2026', 
             'Sylhet Division', 
             'Divisional Administration Sylhet & NMST', 
             'Secured 1st place champion trophy in Sylhet division for outstanding performance in Science, Technology & Robotics innovation.', 
             4),
            ('Divisional & District', 
             '1st Place (District Champion) - 47th National Science & Technology Fair 2026', 
             'Team DEMON71', 
             'Alif Subhan Chowdhury Government College', 
             '2026', 
             'Habiganj District', 
             'District Administration Habiganj & NMST', 
             'Awarded 1st place champion in Habiganj district for smart technology hardware development and software engineering.', 
             5)");
    }

    // Seed Site Settings
    $stmt = $pdo->query("SELECT COUNT(*) FROM site_settings");
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO site_settings (id, site_title, site_logo_text, contact_email, github_url, linkedin_url, twitter_url, footer_copyright, ai_chatbot_enabled, ai_api_key) VALUES 
            (1, 
             'Tarikur Rahman | Senior Full-Stack Web Developer Portfolio', 
             'Tarikur.dev', 
             'tarikur@example.com', 
             'https://github.com/TarikurRahman', 
             'https://linkedin.com', 
             'https://twitter.com', 
             'All rights reserved.',
             1,
             '')");
    } else {
        // Auto-Migration check
        try {
            $pdo->exec("ALTER TABLE site_settings ADD COLUMN ai_chatbot_enabled INTEGER DEFAULT 1");
        } catch (Exception $e) {}
        try {
            $pdo->exec("ALTER TABLE site_settings ADD COLUMN ai_api_key TEXT DEFAULT ''");
        } catch (Exception $e) {}
    }
}

/**
 * Return active PDO instance
 */
function getDB() {
    global $pdo;
    return $pdo;
}
