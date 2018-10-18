<?php

namespace FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends Controller
{

    /**
     * @Route("/", name="homepage")
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $posts = $this->get('AdminBundle\Service\PostManager')->getPublishedPosts();

        return $this->render('@Front/Default/index.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * 
     * @Route("/show/{id}", name="front_post_show")
     */
    public function showAction($id)
    {
        $post = $this->get('AdminBundle\Service\PostManager')->getPublishedPost($id);
        if (!$post) {
            return $this->redirectToRoute('homepage');
        }

        return $this->render('@Front/Default/post.html.twig', [
            'post' => $post
        ]);
    }
    
}