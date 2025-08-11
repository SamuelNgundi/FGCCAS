<?php
// Email address to receive submissions
$receiving_email_address = 'fogccasociety@gmail.com';

// reCAPTCHA secret key (from Google Admin Console)
$recaptcha_secret = "6Lf_UqMrAAAAAIT_R7DBbHcbNsorbj6w5lDGmEAB";

// Validate reCAPTCHA
if (!isset($_POST['g-recaptcha-response'])) {
    die("Captcha not completed.");
}

$recaptcha_response = $_POST['g-recaptcha-response'];
$verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$recaptcha_secret}&response={$recaptcha_response}");
$response_data = json_decode($verify);

if (!$response_data->success) {
    die("Captcha verification failed. Please try again.");
}

// Include PHPMailer form handler
if (file_exists($php_email_form = '../assets/vendor/php-email-form/php-email-form.php')) {
    include($php_email_form);
} else {
    die('Unable to load the "PHP Email Form" Library!');
}

// Create email
$contact = new PHP_Email_Form;
$contact->ajax = true;
$contact->to = $receiving_email_address;

// IMPORTANT: Use your Gmail address as from_email to avoid SPF/DKIM issues
$contact->from_name = $_POST['name'];
$contact->from_email = 'fogccasociety@gmail.com'; // Always your Gmail address
$contact->subject = $_POST['subject'];

// Add reply-to separately (so replies go to the visitor)
$visitor_email = $_POST['email'] ?? '';
$contact->add_message($_POST['name'], 'From');
$contact->add_message($visitor_email, 'Email');
$contact->add_message($_POST['message'], 'Message', 10);

// Send email
echo $contact->send();
?>
