<?php

namespace App\Controller;

use App\Entity\BlogPost;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;

/**
 * Class BlogController
 * @package App\Controller
 * @Route("/blog")
 */
class BlogController extends AbstractController
{
    /**
     * @Route("/{page}", name="blog_list", methods={"GET"}, defaults={"page":1}, requirements={"page"="\d+"})
     * @param int $page
     * @return JsonResponse
     */
    public function list(int $page, Request $request)
    {
        $limit = $request->get('limit', 10); // Get resources limit "?limit=<int>"
        $repository = $this->getDoctrine()->getRepository(BlogPost::class);
        $posts = $repository->findAll();

        return $this->json(
            [
                'page' => $page,
                'limit' => $limit,
                'data'=> array_map(function (BlogPost $post) {
                    // Return items' routes
                    return $this->generateUrl('blog_by_id', ['slug' => $post->getSlug()]);
                }, $posts)
            ]
        );
    }

    /**
     * @Route("/post/{id}", name="blog_by_id", methods={"GET"}, requirements={"id"="\d+"})
     * @ParamConverter("post", class="App:BlogPost")
     * @param BlogPost $post
     * @return JsonResponse
     */
    public function post(BlogPost $post)
    {
        return $this->json(
        // self::POSTS[array_search($id, array_column(self::POSTS, 'id'))]
            $post
        );
    }

    /**
     * @Route("/post/{slug}", name="blog_by_slug", methods={"GET"}, requirements={"slug"="[a-z-\d]+"})
     *
     * The below annotation is not required when $post is typehinted with BlogPost
     * and route parameter name matches any field on the BlogPost entity
     * @ParamConverter("post", class="App:BlogPost", options={"mapping": {"slug": "slug"}})
     * @param string $slug
     * @return JsonResponse
     */
    public function postByBlog(string $slug): JsonResponse
    {
        return $this->json(
        // self::POSTS[array_search($slug, array_column(self::POSTS, 'slug'))]
            $this->getDoctrine()->getRepository(BlogPost::class)->findOneBy(['slug' => $slug])
        );
    }

    /**
     * @Route("/add", name="blog_add", methods={"GET","POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request): JsonResponse
    {
        /** @var Serializer $serializer */
        $serializer = $this->get('serializer');

        /** @see https://symfony.com/doc/current/components/serializer.html */
        $blogPost = $serializer->deserialize($request->getContent(), BlogPost::class, 'json');

        $em = $this->getDoctrine()->getManager();
        $em->persist($blogPost);
        $em->flush();

        return $this->json($blogPost);
    }

    /**
     * @Route("/delete/{id}", name="blog_post_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     * @return JsonResponse
     */
    public function delete(BlogPost $post): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}