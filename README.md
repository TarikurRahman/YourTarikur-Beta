# Web Developer Portfolio

A modern, dynamic personal portfolio website built with native PHP, MySQL/SQLite, Tailwind CSS, and JavaScript. This project is designed to help a developer showcase projects, services, achievements, articles, and contact information in a clean and professional way.

It also includes an admin dashboard so the site owner can manage content without editing code directly.


<!-- Website Full Preview -->
<div align="center">

  ## 🖥️ Live Preview

  <a href="https://tarikurrahman.site.je/" target="_blank">
    <img src="https://img.shields.io/badge/🚀_VISIT_LIVE_SITE-tarikurrahman.site.je-0070f3?style=for-the-badge&logo=googlechrome&logoColor=white" alt="Visit Live Site" />
  </a>

  <br /><br />

  <a href="https://tarikurrahman.site.je/" target="_blank">
    <img src="https://github.com/user-attachments/assets/db48dfd1-1681-41c7-9942-086898402fd0" alt="YourTarikur-Beta Website Preview" width="100%" />
  </a>

</div>

---

## 1. Project Overview

This project is a full-featured developer portfolio website that allows you to:

- Present your profile, skills, and experience in a modern landing page
- Showcase featured projects with descriptions and links
- Display awards, achievements, and services
- Publish articles or blog posts
- Receive messages through a contact form
- Manage website content from a secure admin panel

In simple terms, this is a professional online portfolio website that helps you present your work to clients, employers, and collaborators.

---

## 2. How to Install & Run

### Requirements

Make sure you have the following installed:

- PHP 8.0 or newer
- A web server such as XAMPP, WAMP, Laragon, or the built-in PHP server
- MySQL database (optional, because the project can also fall back to SQLite)

### Step-by-Step Setup

1. Place the project folder in your local web server directory:
   - XAMPP: `htdocs`
   - WAMP: `www`
   - Laragon: `www`

2. Start your web server and database service.

3. Open the project folder in your browser.

4. If you want to use MySQL, import the database schema from `database.sql` into your database.

5. If MySQL is not available, the project can automatically use SQLite as a fallback.

### Run Locally

You can also run the app quickly using PHP's built-in server:

```bash
php -S localhost:8000
```

Then open:

```text
http://localhost:8000
```

You can change these later from the admin settings area.

---

## 3. Features

This project includes the following key features:

- Responsive portfolio homepage
- Dynamic hero section with profile information
- Project showcase section
- Services and skill presentation
- Awards and achievements section
- Articles/blog section
- Contact form with message handling
- Admin dashboard for content management
- Secure authentication for admin access
- MySQL support with SQLite fallback for easier local testing

---

## 4. Project Structure

The project is organized as follows:

```text
admin/          # Admin dashboard and management pages
assets/        # CSS, JavaScript, and uploaded media files
config/        # Database configuration
includes/      # Shared PHP functions and layout components
api/           # Backend API endpoints
index.php      # Main public portfolio page
project-details.php  # Project detail page
article-details.php  # Article detail page
contact-api.php  # Contact form processing
database.sql   # Database schema and sample data
```

---

## 5. About the Developer

Hello! I’m Tarikur Rahman, a developer passionate about building modern web applications, creative digital experiences, and practical software solutions.

- Name: Tarikur Rahman
- GitHub: https://github.com/tarikurrahman
- Portfolio: https://yourtarikur.vercel.app/
- Social/Handle: tarikurrahman08
- Email: tarikurrahman2008@gmail.com

---

## 6. License

This project is licensed under the MIT License.

You are free to use, modify, and distribute this project with attribution.

---

## Quick Note

If you want to customize the content, you can update the site information through the admin panel or by editing the database-backed content.

If you would like, I can also help you create a more advanced README with screenshots, usage examples, and a full tech stack section.
