<?php

namespace App\Controller;

use App\Entity\BlogPost;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;

/**
 * Class BlogController
 * @package App\Controller
 * @Route("/blog")
 */
class BlogController extends AbstractController
{
    private const POSTS = [
        [
            'id' => 1,
            'slug' => 'hello-world',
            'title' => 'Hello World!',
            'author' => 'John Doe'
        ],
        [
            'id' => 2,
            'slug' => 'second-post',
            'title' => 'Second Post!',
            'author' => 'Bob'
        ],
        [
            'id' => 3,
            'slug' => 'third-post',
            'title' => 'Third Post!',
            'author' => 'Franck'
        ],
    ];

    /**
     * @Route("/{page}",
     *     name="blog_list",
     *     methods={"GET"},
     *     defaults={"page":1},
     *     requirements={"page"="\d+"}
     * )
     * @param int $page
     * @return JsonResponse
     */
    public function list(int $page, Request $request)
    {
        $limit = $request->get('limit', 10); // Get resources limit "?limit=<int>"

        return $this->json(
            [
                'page' => $page,
                'limit' => $limit,
                'data'=> array_map(function ($item) {
                    // Return items' routes
                    return $this->generateUrl('blog_by_id', ['id' => $item['id']]);
                },self::POSTS)
            ]
        );
    }

    /**
     * @Route("/post/{id}",
     *     name="blog_by_id",
     *     methods={"GET"},
     *     requirements={"id"="\d+"}
     * )
     * @param int $id
     * @return JsonResponse
     */
    public function post(int $id)
    {
        return $this->json(
            self::POSTS[array_search($id, array_column(self::POSTS, 'id'))]
        );
    }

    /**
     * @Route("/post/{slug}",
     *     name="blog_by_slug",
     *     methods={"GET"},
     *     requirements={"slig"="[a-z]+"}
     * )
     * @param string $slug
     * @return JsonResponse
     */
    public function postByBlog(string $slug)
    {
        return $this->json(
            self::POSTS[array_search($slug, array_column(self::POSTS, 'slug'))]
        );
    }
}