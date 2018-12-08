<?php


namespace App\Controller;


use App\Entity\BlogPost;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/blog")
 */
class BlogController extends AbstractController {
    /**
     * @Route("/", name="blog_list", defaults={"page": 1}, requirements={"page"="\d+"})
     *
     * @param int $page
     * @param Request $request
     * @return JsonResponse
     */
    public function list($page, Request $request)
    {
        $limit = $request->get('limit', 100);
        $repository = $this->getDoctrine()->getRepository(BlogPost::class);
        $items = $repository->findAll();

        return $this->json([
            'page' => $page,
            'limit' => $limit,
            'data' => array_map(function (BlogPost $item) {
                return $this->generateUrl('blog_by_slug', ['slug' => $item->getSlug()]);
            }, $items),
        ]);
    }

    /**
     * @Route("/{id}", name="blog_by_id", requirements={"id"="\d+"})
     * @param BlogPost $post
     * @return JsonResponse
     */
    public function post(BlogPost $post)
    {
        //the same as find($id)
        return $this->json($post);
    }

    /**
     * @Route("/post/{slug}", name="blog_by_slug", methods={"GET"})
     * @param BlogPost $post
     * @return JsonResponse
     */
    public function postBySlug(BlogPost $post)
    {
        return $this->json($post);
    }

    /**
     * @Route("/add", name="add_new_post", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request)
    {
        /** @var Serializer $serializer */
        $serializer = $this->get('serializer');

        $blogPost = $serializer->deserialize($request->getContent(), BlogPost::class, 'json');

        $em = $this->getDoctrine()->getManager();
        $em->persist($blogPost);
        $em->flush();

        return $this->json($blogPost);
    }

    /**
     * @Route("/post/{id}", name="delete_post", methods={"DELETE"})
     * @param BlogPost $post
     * @return JsonResponse
     */
    public function delete(BlogPost $post)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}