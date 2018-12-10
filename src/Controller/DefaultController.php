<?php


namespace App\Controller;


use App\Security\UserConfirmationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class DefaultController extends AbstractController {
    /**
     * @Route("/", name="default_index")
     */
    public function index()
    {
        return $this->render(
            'base.html.twig'
        );
    }

    /**
     * @Route("/confirm-user/{token}", name="default_token_confirmation")
     * @param string $token
     * @param UserConfirmationService $confirmationService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function confirmUser(
        string $token,
        UserConfirmationService $confirmationService
    ) {
        $confirmationService->confirmUser($token);

        return $this->redirectToRoute('default_index');
    }
}