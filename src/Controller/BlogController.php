<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Controller for managing blog articles and comments.
 */
class BlogController extends AbstractController
{
    /**
     * Displays the homepage.
     */
    #[Route('/', name: 'home', methods: ['GET'])]
    public function home(): Response
    {
        return $this->render('blog/home.html.twig');
    }

    /**
     * Lists all articles.
     *
     * @param ArticleRepository $repo The article repository
     */
    #[Route('/blog', name: 'blog_index', methods: ['GET'])]
    public function index(ArticleRepository $repo): Response
    {
        $articles = $repo->findAll();

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles,
        ]);
    }

    /**
     * Creates a new article or updates an existing one.
     *
     * @param Request                $request The HTTP request
     * @param EntityManagerInterface $manager The entity manager
     * @param Article|null           $article The article to edit or null for a new article
     */
    #[Route('/blog/new', name: 'blog_create', methods: ['GET', 'POST'])]
    #[Route('/blog/{id}/edit', name: 'blog_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour créer ou modifier un article')]
    public function createOrUpdate(Request $request, EntityManagerInterface $manager, ?Article $article = null): Response
    {
        if (!$article) {
            $article = new Article();
            $article->setAuthor($this->getUser());
        }

        $isNewArticle = $article->getId() === null;
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Set creation date for new articles
                if ($isNewArticle) {
                    $article->setCreatedAt(new \DateTimeImmutable());
                }

                $manager->persist($article);
                $manager->flush();

                $this->addFlash(
                    'success',
                    $isNewArticle ? 'Article créé avec succès!' : 'Article mis à jour avec succès!'
                );

                return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue : '.$e->getMessage());
            }
        }

        return $this->render('blog/create.html.twig', [
            'formArticle' => $form->createView(),
            'editMode' => !$isNewArticle,
        ]);
    }

    /**
     * Displays a single article and manages its comments.
     *
     * @param Article                $article The article to show
     * @param Request                $request The HTTP request
     * @param EntityManagerInterface $manager The entity manager
     */
    #[Route('/blog/{id}', name: 'blog_show', methods: ['GET', 'POST'])]
    public function show(Article $article, Request $request, EntityManagerInterface $manager): Response
    {
        $comment = new Comment();
        $user = $this->getUser();
        $form = null;
        
        // Only create the form if the user is authenticated
        if ($user) {
            $form = $this->createForm(CommentType::class, $comment);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $comment->setArticle($article)
                           ->setAuthor($user)
                           ->setCreatedAt(new \DateTimeImmutable());

                    $manager->persist($comment);
                    $manager->flush();

                    $this->addFlash('success', 'Commentaire ajouté avec succès!');

                    return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Impossible d\'ajouter le commentaire : '.$e->getMessage());
                }
            }
        }

        return $this->render('blog/show.html.twig', [
            'article' => $article,
            'commentForm' => $form ? $form->createView() : null,
            'isAuthenticated' => $user !== null,
        ]);
    }
}
