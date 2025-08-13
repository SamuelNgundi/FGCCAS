<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

require_once __DIR__ . '/../../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

class PHP_Email_Form {
    public $to;
    public $from_name;
    public $from_email;
    public $subject;
    public $message = '';
    public $ajax = false;

    public function add_message($content, $label = '', $line_break = 1) {
        $this->message .= $label . ": " . $content;
        for ($i = 0; $i < $line_break; $i++) {
            $this->message .= "\n";
        }
    }

    public function send() {
        $mail = new PHPMailer(true);

        try {
            // SMTP settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['GMAIL_USERNAME'];
            $mail->Password   = $_ENV['GMAIL_PASSWORD'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Email headers
            $mail->setFrom($this->from_email, $this->from_name);
            $mail->addAddress($this->to);
            $mail->addReplyTo($this->from_email, $this->from_name);

            // Email content
            $mail->isHTML(false);
            $mail->Subject = $this->subject;
            $mail->Body    = $this->message;

            $mail->send();
            return 'OK';
        } catch (Exception $e) {
            return 'Error: ' . $mail->ErrorInfo;
        }
    }
}
// === Honeypot anti-bot check ===
if (!empty($_POST['hp_field'])) {
    die("Error: Bot detected.");
}