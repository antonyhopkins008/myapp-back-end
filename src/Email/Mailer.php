<?php


namespace App\Email;


use App\Entity\User;

class Mailer {
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * Mailer constructor.
     * @param \Swift_Mailer $mailer
     * @param \Twig_Environment $twig
     */
    public function __construct(
        \Swift_Mailer $mailer,
        \Twig_Environment $twig
    ) {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendConfirmation(User $user)
    {
        $body = $this->twig->render(
            'email/confirmation.email.twig',
            [
                'user' => $user,
            ]
        );

        $message = (new \Swift_Message("Confirmation message"))
            ->setFrom('api-practice@gmail.com')
            ->setTo($user->getEmail())
            ->setBody($body, 'text/html');

        $this->mailer->send($message);
    }
}