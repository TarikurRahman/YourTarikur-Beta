-- Developer Portfolio Database Schema
-- Compatible with MySQL 5.7+ / 8.0+ & SQLite

CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS hero_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    status_text VARCHAR(100) DEFAULT 'Available for work',
    name VARCHAR(100) NOT NULL,
    title VARCHAR(150) NOT NULL,
    subtitle TEXT,
    pitch TEXT,
    cv_url VARCHAR(255) DEFAULT '#',
    profile_image VARCHAR(255) DEFAULT 'assets/images/hero-avatar.png',
    github_username VARCHAR(100) DEFAULT 'TarikurRahman',
    github_token VARCHAR(255) DEFAULT '',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stat_key VARCHAR(50) NOT NULL UNIQUE,
    stat_value VARCHAR(50) NOT NULL,
    stat_label VARCHAR(100) NOT NULL,
    icon VARCHAR(50) DEFAULT 'award',
    display_order INT DEFAULT 0
);

CREATE TABLE IF NOT EXISTS life_gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    caption TEXT,
    image_url VARCHAR(255) NOT NULL,
    location VARCHAR(100) DEFAULT '',
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    slug VARCHAR(150) NOT NULL UNIQUE,
    description TEXT NOT NULL,
    full_description TEXT,
    image_url VARCHAR(255) NOT NULL,
    tech_stack VARCHAR(255) NOT NULL,
    live_url VARCHAR(255) DEFAULT '#',
    github_url VARCHAR(255) DEFAULT '#',
    is_featured TINYINT(1) DEFAULT 1,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    icon VARCHAR(50) DEFAULT 'code',
    display_order INT DEFAULT 0
);

CREATE TABLE IF NOT EXISTS currently_building (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    status VARCHAR(50) DEFAULT 'In Progress',
    progress_percent INT DEFAULT 50,
    tech_stack VARCHAR(255) DEFAULT '',
    icon VARCHAR(50) DEFAULT 'terminal',
    display_order INT DEFAULT 0
);

CREATE TABLE IF NOT EXISTS articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    excerpt TEXT NOT NULL,
    content TEXT NOT NULL,
    thumbnail VARCHAR(255) DEFAULT 'assets/images/default-article.jpg',
    read_time VARCHAR(20) DEFAULT '5 min read',
    published_at DATE NOT NULL,
    is_published TINYINT(1) DEFAULT 1,
    views INT DEFAULT 0
);

CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_name VARCHAR(100) NOT NULL,
    sender_email VARCHAR(100) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Initial Default Data Inserts

-- Default Admin User: admin / admin123
INSERT IGNORE INTO admin_users (id, username, password_hash, email, name) 
VALUES (1, 'admin', '$2y$10$iM1vN8.jL/x4P.Y6v1bAEOz/8rWfD0Z50jKq5yV2uW3xY4zA5B6C7', 'admin@example.com', 'Tarikur Rahman');

-- Initial Hero Info
INSERT IGNORE INTO hero_info (id, status_text, name, title, subtitle, pitch, cv_url, profile_image) 
VALUES (1, 
'Available for work', 
'Tarikur Rahman', 
'Full Stack Developer in Bangladesh', 
'Specializing in high-performance Web Applications, Native PHP, Node.js, and Modern UI/UX Architecture.',
'I craft scalable backend architectures, reactive front-end interfaces, and robust REST APIs with an obsessive focus on performance, clean code, and elegant user experience.',
'#', 
'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=600');

-- Initial Stats
INSERT IGNORE INTO stats (id, stat_key, stat_value, stat_label, icon, display_order) VALUES
(1, 'exp', '2+', 'Years Experience', 'calendar', 1),
(2, 'projects', '25+', 'Projects Completed', 'folder-git-2', 2),
(3, 'clients', '15+', 'Happy Clients', 'users', 3),
(4, 'contributions', '100+', 'Open-Source Contributions', 'git-commit', 4);

-- Initial Services
INSERT IGNORE INTO services (id, title, description, icon, display_order) VALUES
(1, 'Full Stack Web Development', 'End-to-end web apps built with Native PHP, MySQL, React, and modern CSS frameworks optimized for lightning load speeds.', 'code-2', 1),
(2, 'RESTful API & Backend Systems', 'Architecting secure, well-documented API services, authentication pipelines, and database optimizations with PDO.', 'server', 2),
(3, 'AI & Automation Integration', 'Embedding OpenAI / LLM features, AI vision processing, and automated workflow integrations directly into web dashboards.', 'bot', 3),
(4, 'Performance & Security Audit', 'Optimizing database queries, caching strategies, audit logging, SQLi/XSS vulnerability fixes, and Lighthouse 95+ scores.', 'zap', 4);

-- Initial Projects
INSERT IGNORE INTO projects (id, title, slug, description, full_description, image_url, tech_stack, live_url, github_url, is_featured, display_order) VALUES
(1, 
'Antigravity Cloud IDE Portal', 
'antigravity-cloud-ide-portal', 
'A real-time developer environment dashboard with integrated terminal, agent task manager, and code telemetry analytics.',
'Antigravity Cloud IDE Portal is an enterprise-level SaaS dashboard designed for remote engineering teams. Built with Native PHP 8, MySQL PDO, Tailwind CSS, and WebSockets. Features role-based access control, file tree management, live execution logs, and automated code review pipelines.',
'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&q=80&w=800', 
'PHP 8, MySQL, PDO, Tailwind CSS, JavaScript', 
'#', 
'#', 
1, 1),
(2, 
'EcoMetrics SaaS Analytics', 
'ecometrics-saas-analytics', 
'High-throughput telemetry dashboard tracking carbon offset metrics and API resource consumption for cloud workloads.',
'EcoMetrics aggregates microservice logs and infrastructure metrics across multi-cloud clusters into actionable charts. Includes custom reporting, PDF export, scheduled cron jobs, and granular REST endpoint monitoring.',
'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&q=80&w=800', 
'PHP, MySQL, Chart.js, REST API, Tailwind', 
'#', 
'#', 
1, 2),
(3, 
'NexGen E-Commerce Platform', 
'nexgen-e-commerce-platform', 
'Ultra-fast headless online shop engine with inventory sync, automated PDF invoices, and multi-currency payment gateways.',
'A full-featured e-commerce engine equipped with dynamic product variant matrix, Stripe & SSLCommerz checkout hooks, session cart, promo code engine, and a comprehensive admin backend panel.',
'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&q=80&w=800', 
'Native PHP, PDO, MySQL, Alpine.js, Tailwind', 
'#', 
'#', 
1, 3);

-- Initial Life Gallery (Polaroid style)
INSERT IGNORE INTO life_gallery (id, title, caption, image_url, location, display_order) VALUES
(1, 'Sylhet Tea Gardens Trek', 'Exploring the lush green hills and misty trails during a weekend recharge getaway.', 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&q=80&w=600', 'Sylhet, Bangladesh', 1),
(2, 'Late Night Code & Coffee', 'Debugging complex asynchronous algorithms at 2 AM with dark roast coffee.', 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&q=80&w=600', 'Home Studio', 2),
(3, 'Tech Meetup Keynote', 'Sharing best practices on PHP PDO optimization and clean backend architecture.', 'https://images.unsplash.com/photo-1475721027785-f74eccf877e2?auto=format&fit=crop&q=80&w=600', 'Dhaka Tech Hub', 3),
(4, 'Mountain Summit Sunset', 'Reaching the peak after a 5-hour hike — nature is the ultimate inspiration.', 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&q=80&w=600', 'Bandarban Heights', 4);

-- Initial Currently Building
INSERT IGNORE INTO currently_building (id, title, description, status, progress_percent, tech_stack, icon, display_order) VALUES
(1, 'Autonomous AI Workflow Agents', 'Building custom agentic workflows that integrate LLM tool-calling with PHP CLI runners.', 'Active Build', 85, 'PHP 8.2, OpenAI API, cURL', 'bot', 1),
(2, 'Distributed Caching Layer', 'Designing an in-memory key-value cache wrapper for high-frequency PDO query results.', 'Research & Dev', 65, 'PHP, Redis, PDO', 'database', 2),
(3, 'Next.js App Router Masterclass', 'Mastering server components, streaming SSR, and edge middleware optimization.', 'Continuous Learning', 90, 'Next.js 14, React, TypeScript', 'sparkles', 3);

-- Initial Articles
INSERT IGNORE INTO articles (id, title, slug, excerpt, content, thumbnail, read_time, published_at, is_published, views) VALUES
(1, 
'Mastering PHP PDO: Security, Prepared Statements, and Performance Optimization', 
'mastering-php-pdo-security-prepared-statements-performance', 
'Learn how to write rock-solid database access layers in Native PHP using PDO prepared statements to completely eliminate SQL injection threats while maximizing query execution speed.', 
'PHP Data Objects (PDO) provides a lightweight, consistent interface for accessing databases in PHP. Unlike legacy mysql_* functions or standard mysqli, PDO supports multi-database drivers and prepared statements natively.\n\n### Why PDO Prepared Statements Matter\nPrepared statements separate SQL code from user-supplied data. When executing `$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email")`, the database compiles the SQL query structure first. When you execute it with bound parameters, user data is treated strictly as literal values — never as executable SQL commands.\n\n### Best Practices for PDO Security\n1. Always set `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION` to catch connection & query errors gracefully.\n2. Turn off native emulation with `PDO::ATTR_EMULATE_PREPARES => false` to enforce true server-side prepared statements.\n3. Always fetch associative arrays with `PDO::FETCH_ASSOC` to keep payload structures clean.\n\nBy following these principles, your native PHP applications remain fast, robust, and completely secure against SQL injection attacks.', 
'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?auto=format&fit=crop&q=80&w=800', 
'6 min read', 
'2026-08-01', 
1, 142),
(2, 
'Building Dark/Light Theme Switching with Tailwind CSS and LocalStorage', 
'building-dark-light-theme-switching-tailwind-css-localstorage', 
'A step-by-step guide to crafting smooth dark mode toggles with zero layout flash using CSS variables, Tailwind classes, and browser LocalStorage persistence.', 
'Dark mode has transitioned from a trendy feature to an essential accessibility and UI expectation. Implementing a flawless theme toggle requires managing three key aspects: OS color scheme preference (`prefers-color-scheme`), manual toggle state in `localStorage`, and avoiding screen flash during page load.\n\n```js\n// Inline theme initialization before DOM renders to prevent flicker\nif (localStorage.theme === "dark" || (!("theme" in localStorage) && window.matchMedia("(prefers-color-scheme: dark)").matches)) {\n  document.documentElement.classList.add("dark");\n} else {\n  document.documentElement.classList.remove("dark");\n}\n```\n\nCombine this script with Tailwind''s `darkMode: "class"` configuration and CSS transition variables for a seamless, ultra-modern portfolio visual experience.', 
'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?auto=format&fit=crop&q=80&w=800', 
'4 min read', 
'2026-08-05', 
1, 98);

-- 9. AWARDS TABLE
CREATE TABLE IF NOT EXISTS `awards` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category` VARCHAR(100) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `team_name` VARCHAR(100) DEFAULT '',
  `institution` VARCHAR(150) DEFAULT '',
  `event_date` VARCHAR(100) DEFAULT '',
  `location` VARCHAR(150) DEFAULT '',
  `organizer` VARCHAR(150) DEFAULT '',
  `description` TEXT,
  `display_order` INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Initial Awards & Achievements
INSERT IGNORE INTO `awards` (`id`, `category`, `title`, `team_name`, `institution`, `event_date`, `location`, `organizer`, `description`, `display_order`) VALUES
(1, 'National & International', 'Gold Medalist - 8th World Invention Competition and Exhibition (WICE) 2026', 'Team DEMON71', 'Alif Subhan Chowdhury Government College', '9 May 2026', 'Northern University Bangladesh', 'IYSA & Dreams of Bangladesh', 'Awarded Gold Medal in IoT & Robotics category for developing autonomous hardware-software automation and smart robotics systems.', 1),
(2, 'National & International', 'Special 5th Place - 47th National Science & Technology Week 2026', 'Team DEMON71', 'Alif Subhan Chowdhury Government College', 'June 2026', 'Agargaon, Dhaka', 'National Museum of Science & Technology (NMST)', 'Recognized nationally among top young innovators across Bangladesh for smart robotics and embedded systems engineering.', 2),
(3, 'National & International', 'Top 10 Finalist - National Science Fest 2025', 'Team DEMON71', 'Alif Subhan Chowdhury Government College', '2025', 'Dhaka, Bangladesh', 'Bangladesh Science Club', 'Selected as a Top 10 Finalist for designing and deploying an Autonomous Home Automation & Fire Fighting Robot.', 3),
(4, 'Divisional & District', '1st Place (Divisional Champion) - 47th National Science & Technology Fair 2026', 'Team DEMON71', 'Alif Subhan Chowdhury Government College', '2026', 'Sylhet Division', 'Divisional Administration Sylhet & NMST', 'Secured 1st place champion trophy in Sylhet division for outstanding performance in Science, Technology & Robotics innovation.', 4),
(5, 'Divisional & District', '1st Place (District Champion) - 47th National Science & Technology Fair 2026', 'Team DEMON71', 'Alif Subhan Chowdhury Government College', '2026', 'Habiganj District', 'District Administration Habiganj & NMST', 'Awarded 1st place champion in Habiganj district for smart technology hardware development and software engineering.', 5);

-- 10. SITE SETTINGS TABLE
CREATE TABLE IF NOT EXISTS `site_settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `site_title` VARCHAR(255) NOT NULL,
  `site_logo_text` VARCHAR(100) NOT NULL,
  `contact_email` VARCHAR(150) NOT NULL,
  `github_url` VARCHAR(255) DEFAULT '',
  `linkedin_url` VARCHAR(255) DEFAULT '',
  `twitter_url` VARCHAR(255) DEFAULT '',
  `footer_copyright` VARCHAR(255) DEFAULT '',
  `ai_chatbot_enabled` TINYINT(1) DEFAULT 1,
  `ai_api_key` VARCHAR(255) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Initial Site Settings
INSERT IGNORE INTO `site_settings` (`id`, `site_title`, `site_logo_text`, `contact_email`, `github_url`, `linkedin_url`, `twitter_url`, `footer_copyright`, `ai_chatbot_enabled`, `ai_api_key`) VALUES
(1, 'Tarikur Rahman | Senior Full-Stack Web Developer Portfolio', 'Tarikur.dev', 'tarikur@example.com', 'https://github.com/TarikurRahman', 'https://linkedin.com', 'https://twitter.com', 'All rights reserved.', 1, '');
