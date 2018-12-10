<?php


namespace App\Entity;


use Symfony\Component\Validator\Constraints as Assert;

class UserConfirmation {
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=30, max=30)
     */
    private $confirmationToken;

    public function getConfirmationToken(): string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken($confirmationToken): void
    {
        $this->confirmationToken = $confirmationToken;
    }
}