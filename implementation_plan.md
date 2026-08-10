# Implementation Plan - Dynamic Native PHP & MySQL (PDO) Developer Portfolio Website

Build a dynamic, high-performance developer portfolio website using Native PHP, PDO database abstraction (MySQL with automated SQLite fallback for instant zero-config testing), Tailwind CSS, Lucide icons, and modern JavaScript. The architecture includes a public front-end matching reference specifications and a secure admin control panel with full CRUD capabilities.

## User Review Required

> [!NOTE]
> - **Database Compatibility**: The system uses PDO (`config/db.php`). It connects to MySQL by default using credentials defined in configuration, and includes a fallback to SQLite if MySQL is offline or not configured yet. A complete `database.sql` schema and initial dataset is provided.
> - **Security**: Includes session auth, bcrypt password hashing, CSRF token validation, SQL injection prevention via PDO prepared statements, and XSS output encoding.

---

## Proposed System Architecture & Folder Structure

```
Web Developer/
├── config/
│   └── db.php                  # Secure PDO Database Connection (MySQL / SQLite fallback + Auto-seeder)
├── includes/
│   ├── header.php              # Shared Navbar, Meta tags, Theme toggle script
│   ├── footer.php              # CTA Section, Mountain Vector graphic, Footer Links & Scripts
│   └── functions.php           # Helper functions (Sanitization, Auth, Image Upload, Toast alerts)
├── admin/
│   ├── login.php               # Admin Authentication Login page
│   ├── logout.php              # Destroy session & logout
│   ├── dashboard.php            # Overview stats, total messages count, quick management links
│   ├── manage-hero.php         # CRUD / Update Hero section text, badge status, avatar
│   ├── manage-projects.php     # CRUD Projects (Title, Description, Images, Stack tags, Demo links)
│   ├── manage-articles.php     # CRUD Articles (Title, Slug, Excerpt, Content, Thumbnail, Status)
│   ├── manage-services.php     # CRUD Services & Key Stats counters
│   ├── manage-gallery.php      # CRUD "Life Beyond Code" Polaroid photo gallery
│   ├── manage-building.php     # CRUD "Currently Exploring & Building" goals & progress
│   ├── manage-messages.php     # View & Delete contact form submissions
│   └── settings.php            # Admin password change & site settings
├── assets/
│   ├── css/
│   │   └── style.css           # Custom CSS for dark/light themes, animations, glassmorphism
│   ├── js/
│   │   └── main.js             # Theme switcher, AJAX contact form handler, mobile menu, scroll effects
│   └── uploads/                # Directory for user-uploaded project covers & gallery images
├── index.php                   # Front-End Main Portfolio Page (dynamic data fetch)
├── project-details.php         # Dedicated page/modal for viewing project details
├── article-details.php         # Dedicated page for viewing blog post articles
├── contact-api.php             # AJAX backend handler for contact form submissions
└── database.sql                # Complete MySQL Schema & Sample Data insert script
```

---

## Database Architecture (`database.sql`)

Tables:
1. `admin_users`: Stores admin credentials (`username`, `password_hash`, `email`, `name`). Default login: `admin` / `admin123`.
2. `hero_info`: Hero title, subtitle, pitch, profile badge status ("Available for work"), CV download link, avatar image URL.
3. `stats`: Key metrics counters ("2+ Years Experience", "25+ Projects Completed", "15+ Happy Clients", "100+ Contributions").
4. `life_gallery`: "Life Beyond Code" polaroid cards (`title`, `caption`, `image_url`, `location`, `display_order`).
5. `projects`: Portfolio projects (`title`, `slug`, `description`, `full_description`, `image_url`, `tech_stack`, `live_url`, `github_url`, `is_featured`, `display_order`).
6. `services`: Service cards (`title`, `description`, `icon`, `display_order`).
7. `currently_building`: Learning goals & active builds (`title`, `description`, `status`, `progress_percent`, `tech_stack`, `icon`).
8. `articles`: Blog posts (`title`, `slug`, `excerpt`, `content`, `thumbnail`, `read_time`, `published_at`, `is_published`, `views`).
9. `messages`: Contact form submissions (`sender_name`, `sender_email`, `subject`, `message`, `is_read`, `created_at`).

---

## Front-End Sections (`index.php`, `project-details.php`, `article-details.php`)

1. **Header & Sticky Glass Navbar**: Logo (`<Dev/Port>`), Navigation links, and Theme Toggle Switch (Dark/Light with LocalStorage persistence).
2. **Hero Section**: Pulsing "Available for work" status indicator, Name & Title ("Full Stack Developer in Bangladesh"), Pitch paragraph, Action buttons ("See my Work", "Hire Me / Download CV"), Hero Portrait & Mountain Vector overlay.
3. **Stats Counter Bar**: Grid showcasing dynamic metrics (Years Exp, Projects, Clients, Contributions).
4. **"Life Beyond Code" Gallery**: Styled Polaroid-card photo gallery with tilt animation and captions.
5. **Featured Projects**: Responsive cards with cover image, tags, demo/github buttons, and detailed view modal/page.
6. **"What I Can Help You With" (Services)**: Glowing icon-based cards representing skills (Full Stack, API Development, AI Integration, Performance Optimization, Cloud DevOps, UI/UX).
7. **GitHub Activity Heatmap**: Interactive matrix grid visualizing contributions with commit stats & streaks.
8. **"Currently Exploring & Building"**: Real-time progress bars and tech badges for active experiments.
9. **"Latest Writing" (Blog/Articles)**: Dynamic blog grid with read time, date, excerpt, and full view page.
10. **CTA Box & Mountain Vector Footer**: Call to action banner, social media links, copyright year.

---

## Admin Panel Features (`admin/`)

- **Session Authentication & Password Security**: Password hashing using `password_hash($pass, PASSWORD_BCRYPT)`.
- **Dashboard Overview**: Summary statistics (Active Projects, Published Articles, Unread Messages count).
- **Full CRUD Management**:
  - Hero profile & status editor
  - Project manager with image file upload
  - Blog article manager with rich content preview
  - Services & Stats manager
  - Life Beyond Code polaroid uploader
  - Currently Building tracker
  - Contact Messages inbox with single/bulk deletion and unread indicators
- **Admin Settings**: Password update form and profile settings.

---

## Verification Plan

### Automated / Command-Line Testing
1. Run local PHP syntax check on all `.php` files using `php -l`.
2. Test database connection script and schema initialization via CLI `php config/db.php`.
3. Start PHP built-in web server (`php -S localhost:8000`) and test HTTP endpoints using curl or browser.

### Manual Verification
1. Test Dark Mode / Light Mode toggle and local storage persistence.
2. Submit contact form via AJAX and verify response message and entry creation in database `messages` table.
3. Log into `/admin/login.php` with default credentials `admin` / `admin123`.
4. Perform CRUD operations on Projects, Articles, Services, Stats, and Polaroid Gallery.
5. Verify responsive layout across Mobile (375px), Tablet (768px), and Desktop (1440px).
