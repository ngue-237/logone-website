<?php
namespace App\services;
use Twig\Environment;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

class MaillerService{

    private $mailer;
    private $twig;

    public function __construct(
        MailerInterface $mailer,
        Environment $twig 
        )
    {
        $this->twig = $twig;
        $this->mailer = $mailer;

    }

    /**
     * Undocumented function
     *
     * @param string $subject
     * @param string $to
     * @param string $template
     * @param array $parameters
     * @return void
     */
    public function send (
        string $subject, 
        string $to, 
        string $template, 
        array $parameters,
        string $from
    ){
        $email = (new Email())
        ->from($from)
        ->to($to)
        ->subject($subject)
        ->html(
            $this->twig->render($template, $parameters), 'text/html'
        );
        $this->mailer->send($email);
    }
}