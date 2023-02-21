<?php

namespace App\Controller;

use App\Entity\Blog\Post;
use App\Repository\JobRepository;
use App\Repository\PostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class BlogController extends AbstractController
{
    public function __construct(
        private readonly PostRepository $postRepository,
        private readonly JobRepository $jobRepository
    ) {
    }

    #[Route('/blog', name: 'blog_index', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $this->postRepository->createQueryBuilderPublishedPosts();

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('blog/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/blog/{slug}', name: 'blog_post', methods: ['GET'])]
    public function post(Post $post): Response
    {
        if (false === $post->isPublished()) {
            throw $this->createNotFoundException();
        }

        return $this->render('blog/show.html.twig', [
            'post' => $post,
            'latestJobs' => $this->jobRepository->findLastJobs(5),
        ]);
    }
}
