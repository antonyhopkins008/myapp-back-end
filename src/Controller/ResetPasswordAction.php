<?php


namespace App\Controller;


use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordAction {
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var JWTTokenManagerInterface
     */
    private $tokenManager;

    public function __construct(
        ValidatorInterface $validator,
        UserPasswordEncoderInterface $userPasswordEncoder,
        EntityManagerInterface $entityManager,
        JWTTokenManagerInterface $tokenManager
    ) {
        $this->validator = $validator;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->entityManager = $entityManager;
        $this->tokenManager = $tokenManager;
    }

    public function __invoke(User $user)
    {
        $this->validator->validate($user);
        $user->setPassword(
            $this->userPasswordEncoder->encodePassword(
                $user, $user->getNewPassword()
            )
        );

        $user->setPasswordChangedDate(time());

        $this->entityManager->flush();

        $token = $this->tokenManager->create($user);

        return new JsonResponse(['token' => $token]);
    }
}