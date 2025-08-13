<?php
use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// === Honeypot anti-bot check ===
// If hidden field is filled, reject
if (!empty($_POST['hp_field'])) {
    die("Error: Bot detected.");
}

// === Config from .env ===
$receiving_email_address = $_ENV['RECEIVING_EMAIL'];
$recaptcha_secret = $_ENV['RECAPTCHA_SECRET_KEY'];

// === reCAPTCHA validation ===
if (!isset($_POST['g-recaptcha-response'])) {
    die("Error: Captcha not completed.");
}

$recaptcha_response = $_POST['g-recaptcha-response'];
$verify = file_get_contents(
    "https://www.google.com/recaptcha/api/siteverify?secret={$recaptcha_secret}&response={$recaptcha_response}"
);
$response_data = json_decode($verify);

if (!$response_data->success) {
    die("Error: Captcha verification failed.");
}

// === Include email form library ===
$php_email_form_path = realpath(__DIR__ . '/../assets/vendor/php-email-form/php-email-form.php');
if (!$php_email_form_path || !file_exists($php_email_form_path)) {
    die("Error: Email form library not found.");
}
require_once $php_email_form_path;

// === Prepare and send email ===
$contact = new PHP_Email_Form;
$contact->ajax = true;
$contact->to = $receiving_email_address;

// Use Gmail address as sender (avoids SPF/DKIM issues)
$contact->from_name  = $_POST['name'] ?? 'Anonymous';
$contact->from_email = $_ENV['GMAIL_USERNAME'];
$contact->subject    = $_POST['subject'] ?? '(No subject)';

// Add reply-to separately for user’s real email
$visitor_email = $_POST['email'] ?? '';
$contact->add_message($_POST['name'], 'From');
$contact->add_message($visitor_email, 'Email');
$contact->add_message($_POST['message'] ?? '', 'Message', 10);

echo $contact->send();
