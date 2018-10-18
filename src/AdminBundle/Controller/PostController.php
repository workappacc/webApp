<?php

namespace AdminBundle\Controller;

use AdminBundle\Entity\Post;
use AdminBundle\Form\PostType;
use AdminBundle\Service\FileUploader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;

class PostController extends Controller
{
    
    /**
     * @Security("has_role('ROLE_MANAGER')")
     * @Route("/admin/posts/add", name="post_add")
     */
    public function addAction(Request $request, FileUploader $fileUploader)
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $error = null;
            try {
                $this->get('AdminBundle\Service\PostManager')
                    ->addPost($post, $this->getUser(), $fileUploader);
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }

            if ($error) {
                return $this->render('@Admin\Post\add.html.twig', [
                    'form' => $form->createView(),
                    'error' => $error
                ]);   
            } 
            
            return $this->redirectToRoute('admin_posts');
        }

        return $this->render('@Admin\Post\add.html.twig', [
            'form' => $form->createView(),
            'error' => null
        ]);
    }


    /**
     * @Security("has_role('ROLE_MANAGER')")
     * @Route("/admin/posts", name="admin_posts")
     */
    public function showAction(Request $request)
    {
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $posts = $this->get('AdminBundle\Service\PostManager')
            ->getPosts($isAdmin, $this->getUser()->getId());

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $posts,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('@Admin\Post\show.html.twig', array(
            'pagination' => $pagination,
        ));
    }

    /**
     * @Security("has_role('ROLE_MANAGER')")
     * @Route("/admin/posts/show/{id}", name="show_post")
     */    
    public function showPostAction($id)
    {
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $userId = $this->getUser()->getId();
        $post = $this->get('AdminBundle\Service\PostManager')->getPost($id, $isAdmin, $userId);
        if (is_null($post)) {
            return $this->redirectToRoute('admin_posts');
        }

        return $this->render('@Admin\Post\post.html.twig',[
            'post' => $post
        ]);
    }
    
    /**
     * @Security("has_role('ROLE_MANAGER')")
     * @Route("/admin/posts/delete/{id}", name="delete_post")
     */
    public function deleteAction($id)
    {
        $this->get('AdminBundle\Service\PostManager')->deletePost($id);
        
        return $this->redirectToRoute('admin_posts');
    }

    /**
     * @Security("has_role('ROLE_MANAGER')")
     * @Route("/admin/posts/edit/{id}", name="edit_post")
     */
    public function editAction(Request $request, $id, FileUploader $fileUploader)
    {
        $postService = $this->get('AdminBundle\Service\PostManager');
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $userId = $this->getUser()->getId();
        $post = $postService->getPost($id, $isAdmin, $userId);
        $fileName = $post->getImageFile();
        $error = null;
        
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            try {
                $postService->editPost($post, $fileName, $fileUploader);
            } catch (\Exception $e) {
                $error = $e->getMessage();
                
                return $this->render('@Admin\Post\edit.html.twig',[
                    'form' => $form->createView(),
                    'error' => $error
                ]);
            }

            return $this->redirectToRoute('admin_posts');
        }

        return $this->render('@Admin\Post\edit.html.twig',[
            'form' => $form->createView(),
            'error' => $error
        ]);
    }
}
