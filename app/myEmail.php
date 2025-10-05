<?php
namespace App;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// require_once "config.php";
// require_once "./vendor/autoload.php";
// require_once baseUri() . "PHPMailer/SMTP.php";
// require_once baseUri() . "PHPMailer/Exception.php";

class myEmail {
    private $mailer;

    public function __construct() {
        $this->mailer = new PHPMailer(true);
        $this->setup();
    }

    private function setup() {
        $cfg = $GLOBALS['MAIL'];

        $this->mailer->isSMTP();
        $this->mailer->Host       = $cfg['host'];
        $this->mailer->SMTPAuth   = true;
        $this->mailer->Username   = $cfg['username'];
        $this->mailer->Password   = $cfg['password'];
        $this->mailer->SMTPSecure = $cfg['secure'];
        $this->mailer->Port       = $cfg['port'];

        $this->mailer->setFrom($cfg['from'], $cfg['fromName']);
        $this->mailer->addReplyTo($cfg['replyTo']);
        $this->mailer->isHTML(true);
    }

    public function send(array $data) {
        // Required
        if (empty($data['to']) || empty($data['subject']) || empty($data['body'])) {
            throw new \Exception("Missing required keys: to, subject, body");
        }

        $this->mailer->clearAllRecipients();
        $this->mailer->addAddress($data['to'], $data['fullname'] ?? '');

        // Optional
        if (!empty($data['cc'])) $this->mailer->addCC($data['cc']);
        if (!empty($data['bcc'])) $this->mailer->addBCC($data['bcc']);
        if (!empty($data['attachments'])) {
            foreach ($data['attachments'] as $file) {
                $this->mailer->addAttachment($file);
            }
        }

        $this->mailer->Subject = $data['subject'];

        // Prepare template
        $template = file_get_contents("emailTemplate.php");

        $logo = $data['logo'] ?? "../assets/images/logo.png";
        $fullname = $data['fullname'] ?? "User";
        $body = $data['body'];

        // Button logic
        $button = "";
        if (!empty($data['buttonText']) && !empty($data['buttonLink'])) {
            $button = '<a href="' . htmlspecialchars($data['buttonLink']) . '" class="btn">' 
                    . htmlspecialchars($data['buttonText']) . '</a>';
        }

        // Replace placeholders
        $htmlBody = str_replace(
            ['{{subject}}','{{fullname}}','{{body}}','{{button}}','{{logo}}'],
            [$data['subject'],$fullname,$body,$button,$logo],
            $template
        );

        $this->mailer->Body    = $htmlBody;
        $this->mailer->AltBody = $data['altBody'] ?? strip_tags($body);

        try {
            return $this->mailer->send();
        } catch (Exception $e) {
            throw new \Exception("Mailer Error: " . $this->mailer->ErrorInfo);
        }
    }
}
