<?php
/**
 * AJAX Contact Form Submission API Endpoint
 */

header('Content-Type: application/json');

require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
    exit;
}

$name = trim($_POST['sender_name'] ?? '');
$email = trim($_POST['sender_email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

// Validation
if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Please provide a valid email address.']);
    exit;
}

try {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO messages (sender_name, sender_email, subject, message, is_read) VALUES (:name, :email, :subject, :message, 0)");
    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':subject' => $subject,
        ':message' => $message
    ]);

    echo json_encode([
        'status' => 'success',
        'message' => 'Thank you, ' . htmlspecialchars($name) . '! Your message has been transmitted successfully. I will get back to you soon.'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error occurred while processing your message.']);
}
