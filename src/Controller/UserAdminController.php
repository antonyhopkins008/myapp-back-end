<?php


namespace App\Controller;


use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserAdminController extends AdminController {

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @param User $entity
     */
    protected function updateEntity($entity)
    {
        $this->encodePassword($entity);
        parent::updateEntity($entity);
    }

    /**
     * @param User $entity
     */
    private function encodePassword($entity): void
    {
        $entity->setPassword(
            $this->encoder->encodePassword($entity, $entity->getPassword())
        );
    }

    /**
     * @param User $entity
     */
    protected function persistEntity($entity)
    {
        $this->encodePassword($entity);
        parent::persistEntity($entity);
    }
}