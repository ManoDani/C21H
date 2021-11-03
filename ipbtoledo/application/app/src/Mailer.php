<?php
declare(strict_types=1);

namespace Farol360\AncoraEad;

use PHPMailer;

class Mailer
{
    protected $mailer;

    public function __construct(PHPMailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function send(string $name, string $email, string $subject, string $body)
    {
        $this->mailer->addAddress($email, $name);
        $this->mailer->Subject = $subject;
        $this->mailer->Body = $body;
        return $this->mailer->send();
    }
}
