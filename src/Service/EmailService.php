<?php

namespace App\Service;

use App\Entity\Admin;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Symfony\Component\HttpFoundation\Request;

class EmailService
{
    private MailerInterface $mailer;
    private Environment $twig;
    private string $fromEmail;
    private string $frontendUrl;

    public function __construct(MailerInterface $mailer, Environment $twig, string $fromEmail, string $frontendUrl)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->fromEmail = $fromEmail;
        $this->frontendUrl = $frontendUrl;
    }

    public function sendPasswordResetEmail(Admin $admin, string $token, Request $request): bool
    {
        try {
            $resetUrl = $this->frontendUrl . '/pages/admin/auth/reset-password.php?token=' . $token;
            
            // Log des informations de debug
            error_log('Tentative d\'envoi d\'email à: ' . $admin->getEmail());
            error_log('From email: ' . $this->fromEmail);
            error_log('Frontend URL: ' . $this->frontendUrl);
            error_log('Reset URL: ' . $resetUrl);
            
            $htmlContent = $this->twig->render('emails/reset_password.html.twig', [
                'admin' => $admin,
                'resetUrl' => $resetUrl,
                'token' => $token
            ]);

            $email = (new Email())
                ->from($this->fromEmail)
                ->to($admin->getEmail())
                ->subject('Réinitialisation de votre mot de passe')
                ->html($htmlContent);

            error_log('Email créé, tentative d\'envoi...');
            $this->mailer->send($email);
            error_log('Email envoyé avec succès !');
            
            return true;
        } catch (\Exception $e) {
            // Log l'erreur avec plus de détails
            error_log('Erreur envoi email: ' . $e->getMessage());
            error_log('Type d\'exception: ' . get_class($e));
            error_log('Stack trace: ' . $e->getTraceAsString());
            return false;
        }
    }
}
