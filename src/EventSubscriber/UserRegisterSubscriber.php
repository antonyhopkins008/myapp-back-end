<?php


namespace App\EventSubscriber;


use ApiPlatform\Core\EventListener\EventPriorities;
use App\Email\Mailer;
use App\Entity\User;
use App\Security\TokenGenerator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserRegisterSubscriber implements EventSubscriberInterface {
    private $encoder;
    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * PasswordEventSubscriber constructor.
     * @param UserPasswordEncoderInterface $encoder
     * @param TokenGenerator $tokenGenerator
     * @param Mailer $mailer
     */
    public function __construct(
        UserPasswordEncoderInterface $encoder,
        TokenGenerator $tokenGenerator,
        Mailer $mailer
    ) {
        $this->encoder = $encoder;
        $this->tokenGenerator = $tokenGenerator;
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => [
                'userRegistered',
                EventPriorities::PRE_WRITE,
            ],
        ];
    }

    public function userRegistered(GetResponseForControllerResultEvent $event)
    {
        $user = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$user instanceof User || !in_array($method, [Request::METHOD_POST])) {
            return;
        }

        $user->setPassword($this->encoder->encodePassword($user, $user->getPassword()));

        $user->setConfirmationToken($this->tokenGenerator->getRandomToken());

        $this->mailer->sendConfirmation($user);
    }
}