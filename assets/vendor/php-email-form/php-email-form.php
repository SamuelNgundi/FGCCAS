<?php
class PHP_Email_Form {
    public $to;
    public $from_name;
    public $from_email;
    public $subject;
    public $message;
    public $ajax = false;

    public function add_message($content, $label = '', $line_break = 1) {
        $this->message .= $label . ": " . $content;
        for ($i = 0; $i < $line_break; $i++) {
            $this->message .= "\n";
        }
    }

    public function send() {
        $headers = 'From: ' . $this->from_name . ' <' . $this->from_email . '>' . "\r\n";
        $headers .= 'Reply-To: ' . $this->from_email . "\r\n";
        $headers .= 'X-Mailer: PHP/' . phpversion();

        if (mail($this->to, $this->subject, $this->message, $headers)) {
            return 'OK';
        } else {
            return 'Error';
        }
    }
}
?>
