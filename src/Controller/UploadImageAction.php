<?php


namespace App\Controller;


use ApiPlatform\Core\Validator\Exception\ValidationException;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Image;
use App\Form\ImageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class UploadImageAction {
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var ValidatorInterface
     */
    private $validator;


    /**
     * UploadImageAction constructor.
     * @param FormFactoryInterface $formFactory
     * @param EntityManagerInterface $manager
     * @param ValidatorInterface $validator
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        EntityManagerInterface $manager,
        ValidatorInterface $validator
    ) {
        $this->formFactory = $formFactory;
        $this->manager = $manager;
        $this->validator = $validator;
    }

    public function __invoke(Request $request)
    {
        $image = new Image();

        $form = $this->formFactory->create(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($image);
            $this->manager->flush();

            $image->setFile(null);

            return $image;
        }

        throw new ValidationException(
            $this->validator->validate($image)
        );

    }
}