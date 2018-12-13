<?php


namespace App\Security;


use App\Exception\InvalidConfirmationTokenException;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class UserConfirmationService {
    /**
     * @var UserRepository
     */
    private $repository;
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        UserRepository $repository,
        EntityManagerInterface $manager,
        LoggerInterface $logger
    ) {
        $this->repository = $repository;
        $this->manager = $manager;
        $this->logger = $logger;
    }

    public function confirmUser(string $confirmationToken)
    {
        $this->logger->debug('Getting user by confirmation token');

        $user = $this->repository->findOneBy(['confirmationToken' => $confirmationToken]);

        if (!$user) {
            $this->logger->debug('User by confirmation token not found');
            throw new InvalidConfirmationTokenException();
        }
        $user->setEnabled(true);
        $user->setConfirmationToken(null);
        $this->manager->persist($user);
        $this->manager->flush();

        $this->logger->debug('Confirmed use by confirmation token');
    }
}