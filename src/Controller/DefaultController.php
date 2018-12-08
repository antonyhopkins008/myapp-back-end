<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class DefaultController extends AbstractController {
    /**
     * @Route("/", name="default_controller")
     */
    public function index()
    {
        return new JsonResponse([
            'method' => 'index',
            'time' => time(),
        ]);
    }
}