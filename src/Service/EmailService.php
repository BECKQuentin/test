<?php

namespace App\Service;

use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class EmailService
{

    const EMAIL_ADMIN       = 'email_admin';
    const EMAIL_DEVELOPER   = 'email_developer';

    public function __construct(
        private MailerInterface $mailer,
        private string $emailAdmin,
        private string $emailServer,
        private string $emailDeveloper,
        private string $appEnv,
        private LoggerInterface $logger
    ){}

    public function send(array $data, array $attachments = []): bool
    {
        if ($this->appEnv === 'dev') {
            if (!isset($data['subject'])) {
                throw new Exception("You should specify a subject");
            }
            $data['to'] = $this->emailDeveloper;
        }
        if ($data['to'] === self::EMAIL_DEVELOPER) $data['to'] = $this->emailDeveloper;
        if ($data['to'] === self::EMAIL_ADMIN) $data['to'] = $this->emailAdmin;

        $email = (new TemplatedEmail())
            ->from($data['from'] ?? $this->emailServer)
            ->to($data['to'] ?? $this->emailAdmin)
            ->replyTo($data['replyTo'] ?? $data['from'] ?? $this->emailAdmin)
            ->subject($data['subject'] ?? 'Coprotec | ELAN')
            ->htmlTemplate($data['template'])
            ->context($data['context'] ?? [])
        ;

        foreach ($attachments as $attachment) {
            $email->attachFromPath($attachment);
        }

        try {
            $this->mailer->send($email);
            return true;
        } catch (TransportExceptionInterface $e) {
            // some error prevented the email sending; display an
            // error message or try to resend the message
            $this->logger->alert(sprintf("%s in %s at %s : %s", __FUNCTION__, __FILE__, __LINE__, $e->getMessage()));
        }
        return false;
    }

}