<?php

namespace FrontBundle\Controller;

use AdminBundle\Service\PostManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends Controller
{

    /**
     * @Route("/", name="homepage")
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(PostManager $postManager)
    {
        $posts = $postManager->getPublishedPosts();

        return $this->render('@Front/Default/index.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * 
     * @Route("/show/{id}", name="front_post_show")
     */
    public function showAction($id, PostManager $postManager)
    {
        $post = $postManager->getPublishedPost($id);
        if (!$post) {
            throw $this->createNotFoundException();
        }

        return $this->render('@Front/Default/post.html.twig', [
            'post' => $post
        ]);
    }
    
}