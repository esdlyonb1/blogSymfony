<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/posts')]

class PostController extends AbstractController
{
    #[Route('/', name: 'index_posts')]
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('post/index.html.twig', [
            'posts'=>$postRepository->findAll(),
        ]);
    }



    #[Route("/{id}", name:"show_post")]

    public function show(Post $post):Response
    {

        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);

        return $this->renderForm('post/show.html.twig', [
            'post'=>$post,
            'commentForm'=>$commentForm
        ]);
    }

    #[Route("/create", name:"create_post", priority: 2)]
    #[Route("/edit/{id}", name:"edit_post", priority: 2)]

    public function create(Request $request, EntityManagerInterface $manager, Post $post=null):Response
    {
        $edit = false;
        if($post){
            $edit = true;
        }
            if(!$edit){
                $post = new Post();

            }
        $formPost = $this->createForm(PostType::class,$post);

            $formPost->handleRequest($request);
            if($formPost->isSubmitted() && $formPost->isValid())
            {
                if(!$edit){
                    $post->setCreatedAt(new \DateTime());
                }

                $manager->persist($post);
                $manager->flush();

                return $this->redirectToRoute('show_post', ['id'=>$post->getId()]);

            }


        return $this->renderForm('post/create.html.twig', [
            'formPost'=>$formPost,
            'edit'=>$edit
        ]);
    }
}