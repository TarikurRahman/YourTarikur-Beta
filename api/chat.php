<?php
/**
 * Dynamic AI Assistant Chat API Endpoint
 * Handles user inquiries using Gemini/OpenAI API or Intelligent Local Knowledge Engine Fallback.
 * Includes comprehensive knowledge from Website DB, GitHub, LinkedIn, Facebook, Instagram, TikTok & Twitter.
 */

// Disable HTML error output to prevent corrupting JSON API response
ini_set('display_errors', 0);
error_reporting(0);

header('Content-Type: application/json; charset=utf-8');

try {
    require_once __DIR__ . '/../includes/functions.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method. Please use POST.']);
        exit;
    }

    // 1. Read input JSON or POST data
    $raw_input = file_get_contents('php://input');
    $data = json_decode($raw_input, true);
    if (!is_array($data)) {
        $data = $_POST;
    }

    $user_message = trim($data['message'] ?? '');

    if (empty($user_message)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid question or message.']);
        exit;
    }

    // 2. Fetch site settings & database content
    $site_settings = get_site_settings();
    $ai_enabled = intval($site_settings['ai_chatbot_enabled'] ?? 1);
    $api_key = trim($site_settings['ai_api_key'] ?? '');

    if (!$ai_enabled) {
        echo json_encode([
            'success' => true,
            'reply' => "I am currently offline. You can reach out directly via the contact form on this page!"
        ]);
        exit;
    }

    // Dynamic database records & Social Media URLs
    $hero = get_hero_info();
    $projects = get_projects(false);
    $services = get_services();
    $awards = get_awards();

    $hero_name = $hero['name'] ?? 'Tarikur Rahman';
    $hero_title = $hero['title'] ?? 'Robotics Inventor & Full-Stack Web Developer';
    $hero_subtitle = $hero['subtitle'] ?? 'Specializing in high-performance Web Applications, Native PHP, Node.js, and Modern UI/UX Architecture.';
    $hero_pitch = $hero['pitch'] ?? 'Building scalable backend architectures, reactive front-end interfaces, and robust REST APIs.';
    $contact_email = $site_settings['contact_email'] ?? 'admin@example.com';
    
    // Social Media Profiles
    $github_url    = !empty($site_settings['github_url'])    ? $site_settings['github_url']    : 'https://github.com/tarikurrahman';
    $linkedin_url  = !empty($site_settings['linkedin_url'])  ? $site_settings['linkedin_url']  : 'https://www.linkedin.com/in/tarikurrahman08';
    $facebook_url  = !empty($site_settings['facebook_url'])  ? $site_settings['facebook_url']  : 'https://www.facebook.com/tarikurrahman08';
    $instagram_url = !empty($site_settings['instagram_url']) ? $site_settings['instagram_url'] : 'https://www.instagram.com/tarikurrahman08';
    $tiktok_url    = !empty($site_settings['tiktok_url'])    ? $site_settings['tiktok_url']    : 'https://www.tiktok.com/@tarikurrahman.bd';
    $twitter_url   = !empty($site_settings['twitter_url'])   ? $site_settings['twitter_url']   : 'https://x.com/tarikurrahman08';
    $website_url   = !empty($site_settings['website_url'])   ? $site_settings['website_url']   : 'https://yourtarikur.vercel.app';

    // Format text summaries
    $projects_text = "";
    foreach ($projects as $idx => $p) {
        $projects_text .= ($idx + 1) . ". **" . $p['title'] . "**: " . $p['description'] . " (Tech: " . $p['tech_stack'] . ")\n";
    }

    $awards_text = "";
    foreach ($awards as $idx => $a) {
        $awards_text .= ($idx + 1) . ". **" . $a['title'] . "** (" . $a['event_date'] . ", " . $a['location'] . "): " . $a['description'] . "\n";
    }

    $services_text = "";
    foreach ($services as $idx => $s) {
        $services_text .= "• **" . $s['title'] . "**: " . $s['description'] . "\n";
    }

    // Comprehensive System Prompt
    $system_prompt = "You are Tarikur Rahman's AI Assistant. Answer all user queries using information from his website, LinkedIn, GitHub, Facebook, Instagram, TikTok, and Twitter profiles.

BIO & HEADLINE:
Name: {$hero_name}
Role: {$hero_title}
Bio: {$hero_subtitle} {$hero_pitch}
Primary Email: {$contact_email}

SOCIAL MEDIA & WEB PROFILES:
- GitHub (Repositories & Code): {$github_url}
- LinkedIn (Career & Certifications): {$linkedin_url}
- Facebook (Developer Activity & Tech Events): {$facebook_url}
- Instagram (Personal Photos & Hobby): {$instagram_url}
- TikTok (Short Form Tech Clips): {$tiktok_url}
- X / Twitter (Tech Insights): {$twitter_url}
- Live Portfolio App: {$website_url}

AWARDS & RECOGNITION:
{$awards_text}

FEATURED PROJECTS:
{$projects_text}

SERVICES & TECHNICAL CAPABILITIES:
{$services_text}

INSTRUCTIONS FOR AI:
1. Always respond in a polite, professional, and knowledgeable tone as Tarikur's personal representative.
2. If someone asks for his social links or work history on GitHub, LinkedIn, or Facebook, provide accurate information along with the direct profile URLs.
3. Direct any contract or employment inquiries to his email ({$contact_email}) or the contact section below.
4. Highlight his Gold Medal win at the 8th World Invention Competition and Exhibition (WICE) 2026 whenever relevant.";

    $ai_reply = null;

    // 3. Attempt External LLM API if Key is configured
    if (!empty($api_key)) {
        if (strpos($api_key, 'AIza') === 0 || strlen($api_key) > 30) {
            // Gemini API Call
            $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . urlencode($api_key);
            $payload = [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $system_prompt . "\n\nUser Question: " . $user_message]
                        ]
                    ]
                ]
            ];

            $ch = curl_init($endpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_TIMEOUT, 8);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code === 200 && $response) {
                $json = json_decode($response, true);
                $ai_reply = trim($json['candidates'][0]['content']['parts'][0]['text'] ?? '');
            }
        } elseif (strpos($api_key, 'sk-') === 0) {
            // OpenAI API Call
            $endpoint = "https://api.openai.com/v1/chat/completions";
            $payload = [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => $system_prompt],
                    ['role' => 'user', 'content' => $user_message]
                ],
                'temperature' => 0.7
            ];

            $ch = curl_init($endpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $api_key
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 8);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code === 200 && $response) {
                $json = json_decode($response, true);
                $ai_reply = trim($json['choices'][0]['message']['content'] ?? '');
            }
        }
    }

    // 4. Enhanced Intelligent Local Knowledge Engine (Fallback)
    if (empty($ai_reply)) {
        $q = strtolower($user_message);

        // A. Social Media & Online Profiles
        if (preg_match('/(social|link|links|media|facebook|linkedin|github|instagram|tiktok|twitter|x\.com|vercel|website|url|profile)/i', $q)) {
            if (preg_match('/github/i', $q)) {
                $ai_reply = "💻 **Tarikur's GitHub Profile & Code Repositories:**\n\n• **GitHub**: `{$github_url}`\n\nCheck out his open-source code contributions, robotics firmware, and full-stack web applications!";
            } elseif (preg_match('/linkedin/i', $q)) {
                $ai_reply = "💼 **Tarikur's LinkedIn Career Profile:**\n\n• **LinkedIn**: `{$linkedin_url}`\n\nExplore his professional engineering experience, robotics research achievements, and certifications!";
            } elseif (preg_match('/facebook/i', $q)) {
                $ai_reply = "🌐 **Tarikur's Facebook Profile:**\n\n• **Facebook**: `{$facebook_url}`\n\nFollow his latest tech project showcases, event participation, and engineering activities!";
            } else {
                $ai_reply = "🌐 **Tarikur's Official Online & Social Media Profiles:**\n\n"
                          . "• **GitHub**: `{$github_url}`\n"
                          . "• **LinkedIn**: `{$linkedin_url}`\n"
                          . "• **Facebook**: `{$facebook_url}`\n"
                          . "• **Instagram**: `{$instagram_url}`\n"
                          . "• **TikTok**: `{$tiktok_url}`\n"
                          . "• **Twitter / X**: `{$twitter_url}`\n"
                          . "• **Vercel Web App**: `{$website_url}`";
            }
        }
        // B. Who is Tarikur / Bio / Background / About
        elseif (preg_match('/(who is|who\'s|tell me about|about|bio|background|identity|person|overview|summary|tarikur|profile)/i', $q) && !preg_match('/(project|award|wice|hire|contact|service)/i', $q)) {
            $ai_reply = "👤 **About {$hero_name}:**\n\n{$hero_name} is a **{$hero_title}**. {$hero_subtitle}\n\n{$hero_pitch}\n\n🏆 **Key Highlights:**\n• **Gold Medalist** – 8th World Invention Competition & Exhibition (WICE) 2026\n• **Special 5th Place** – 47th National Science & Technology Week 2026\n\n🌐 **Connect with Tarikur:**\n• **GitHub**: `{$github_url}`\n• **LinkedIn**: `{$linkedin_url}`\n• **Facebook**: `{$facebook_url}`";
        }
        // C. Projects / Software / Portfolio / Apps
        elseif (preg_match('/(project|portfolio|built|software|app|application|code|work)/i', $q)) {
            $ai_reply = "💻 **Featured Projects by {$hero_name}:**\n\n{$projects_text}\nYou can inspect open-source repositories on GitHub (`{$github_url}`) or check out live demos above!";
        }
        // D. Awards / Achievements / WICE / Competition / Medals
        elseif (preg_match('/(award|wice|gold|medal|achievement|honor|competition|contest|place|win|winner)/i', $q)) {
            $ai_reply = "🏆 **Awards & National Achievements:**\n\n{$awards_text}\n{$hero_name} actively participates in robotics, IoT, and software innovation engineering competitions.";
        }
        // E. Services / Skills / Tech Stack / Languages
        elseif (preg_match('/(service|capability|skill|stack|tech|technology|language|php|mysql|javascript|tailwind|react)/i', $q)) {
            $ai_reply = "🚀 **Core Services & Capabilities:**\n\n{$services_text}\n**Technical Expertise**: Native PHP 8, MySQL PDO, Tailwind CSS, ES6+ JavaScript, REST APIs, and Secure Admin Portals.";
        }
        // F. Hire / Contact / Email / Freelance / Reach out
        elseif (preg_match('/(hire|contract|contact|email|reach|freelance|job|work with|talk|message)/i', $q)) {
            $ai_reply = "🤝 **Work & Collaborate with {$hero_name}:**\n\n{$hero_name} is available for engineering contracts and web development projects.\n\n• **Email**: `{$contact_email}`\n• **LinkedIn**: `{$linkedin_url}`\n• **GitHub**: `{$github_url}`\n\nYou can also submit an inquiry directly using the **Contact Form** at the bottom of the page!";
        }
        // G. Greetings
        elseif (preg_match('/^(hi|hello|hey|greetings|good morning|good evening|wassup)\b/i', $q)) {
            $ai_reply = "Hello! 👋 I am Tarikur's AI Portfolio Assistant. How can I help you today? Ask me about his **Bio**, **WICE 2026 Gold Medal**, **Featured Projects**, **Social Profiles**, or **How to Hire Him**!";
        }
        // H. Fallback tailored response
        else {
            $ai_reply = "I am **{$hero_name}'s AI Assistant**. Here is a quick snapshot:\n\n"
                      . "• **Role**: {$hero_title}\n"
                      . "• **Top Recognition**: Gold Medalist – 8th World Invention Competition (WICE) 2026\n"
                      . "• **GitHub**: `{$github_url}`\n"
                      . "• **LinkedIn**: `{$linkedin_url}`\n"
                      . "• **Contact**: `{$contact_email}`\n\n"
                      . "Try asking: *\"What are Tarikur's social media links?\"*, *\"What projects has he built?\"*, or *\"Tell me about his WICE 2026 Gold Medal\"*";
        }
    }

    echo json_encode([
        'success' => true,
        'reply' => $ai_reply
    ]);

} catch (Throwable $t) {
    echo json_encode([
        'success' => true,
        'reply' => "I am Tarikur's AI Assistant. Feel free to contact Tarikur directly at admin@example.com or check out his social media profiles on GitHub and LinkedIn!"
    ]);
}
