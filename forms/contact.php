<?php
// -------------------------------
// CONFIGURATION
// -------------------------------
$receiving_email_address = 'fogccasociety@gmail.com'; // Recipient email
$recaptcha_secret = "6Lf_UqMrAAAAAIT_R7DBbHcbNsorbj6w5lDGmEAB"; // Google reCAPTCHA secret key

// -------------------------------
// reCAPTCHA VALIDATION
// -------------------------------
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

// -------------------------------
// LOAD EMAIL FORM CLASS
// -------------------------------
$php_email_form_path = realpath(__DIR__ . '/../assets/vendor/php-email-form/php-email-form.php');
if (!$php_email_form_path || !file_exists($php_email_form_path)) {
    die("Error: Email form library not found at {$php_email_form_path}");
}

require_once $php_email_form_path;

if (!class_exists('PHP_Email_Form')) {
    die("Error: PHP_Email_Form class not loaded. Check php-email-form.php.");
}

// -------------------------------
// CREATE EMAIL
// -------------------------------
$contact = new PHP_Email_Form;
$contact->ajax = true;
$contact->to = $receiving_email_address;

// Always send from your Gmail address to avoid SPF/DKIM issues
$contact->from_name  = $_POST['name'] ?? 'Anonymous';
$contact->from_email = 'fogccasociety@gmail.com'; // Must be your Gmail address
$contact->subject    = $_POST['subject'] ?? '(No subject)';

// Add reply-to separately so replies go to the visitor
$visitor_email = $_POST['email'] ?? '';
$contact->add_message($_POST['name'], 'From');
$contact->add_message($visitor_email, 'Email');
$contact->add_message($_POST['message'] ?? '', 'Message', 10);

// -------------------------------
// SEND EMAIL
// -------------------------------
$result = $contact->send();
if ($result === 'OK') {
    echo "Message sent successfully.";
} else {
    echo "Error sending message: " . htmlspecialchars($result);
}
?>
