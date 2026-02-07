<?php
/**
 * Contact Form Handler for ITB Nigeria Ltd
 * Sends form submissions to: info@itbng.com
 */

// Prevent direct access
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Method Not Allowed');
}

// Set response header
header('Content-Type: application/json');

// Configuration
$to = "info@itbng.com";  // ✅ Verified from original site
$subject = "Contact Form Submission - ITB Nigeria";
$from_email = "noreply@itbng.com";  // Using domain email as sender

// Sanitize and validate input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Get form data
$name = isset($_POST['Name']) ? sanitize_input($_POST['Name']) : '';
$email = isset($_POST['Email']) ? sanitize_input($_POST['Email']) : '';
$phone = isset($_POST['Phone']) ? sanitize_input($_POST['Phone']) : '';
$message = isset($_POST['Message']) ? sanitize_input($_POST['Message']) : '';

// Validation
$errors = [];

if (empty($name)) {
    $errors[] = "Name is required";
}

if (empty($email)) {
    $errors[] = "Email is required";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format";
}

if (empty($phone)) {
    $errors[] = "Phone is required";
}

if (empty($message)) {
    $errors[] = "Message is required";
}

// If validation fails
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'errors' => $errors
    ]);
    exit;
}

// Build email message
$email_body = "New Contact Form Submission\n";
$email_body .= "================================\n\n";
$email_body .= "Name: $name\n";
$email_body .= "Email: $email\n";
$email_body .= "Phone: $phone\n";
$email_body .= "Message:\n$message\n\n";
$email_body .= "================================\n";
$email_body .= "Submitted: " . date('Y-m-d H:i:s') . "\n";
$email_body .= "IP Address: " . $_SERVER['REMOTE_ADDR'] . "\n";

// Email headers
$headers = "From: $from_email\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// Send email
$mail_sent = mail($to, $subject, $email_body, $headers);

if ($mail_sent) {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Thank you for contacting us! We will get back to you soon.'
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Sorry, there was an error sending your message. Please try again later.'
    ]);
}
?>
