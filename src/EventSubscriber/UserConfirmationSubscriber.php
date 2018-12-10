<?php


namespace App\EventSubscriber;


use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\UserConfirmation;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class UserConfirmationSubscriber implements EventSubscriberInterface {
    /**
     * @var UserRepository
     */
    private $repository;
    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * UserConfirmationSubscriber constructor.
     * @param UserRepository $repository
     * @param EntityManagerInterface $manager
     */
    public function __construct(
        UserRepository $repository,
        EntityManagerInterface $manager
    ) {
        $this->repository = $repository;
        $this->manager = $manager;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['confirmUser', EventPriorities::POST_VALIDATE],
        ];
    }

    public function confirmUser(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();

        if ('api_user_confirmations_post_collection' !== $request->get('_route')) {
            return;
        }

        /** @var UserConfirmation $confirmationToken */
        $confirmationToken = $event->getControllerResult();

        $user = $this->repository->findOneBy(['confirmationToken' => $confirmationToken->getConfirmationToken()]);

        if (!$user) {
            throw new NotFoundHttpException();
        }
        $user->setEnabled(true);
        $user->setConfirmationToken(null);
        $this->manager->persist($user);
        $this->manager->flush();

        $event->setResponse(new JsonResponse(null, Response::HTTP_OK));
    }
}