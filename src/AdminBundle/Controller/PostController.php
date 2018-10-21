<?php

namespace AdminBundle\Controller;

use AdminBundle\Entity\Post;
use AdminBundle\Form\PostType;
use AdminBundle\Security\Voter\PageVoter;
use AdminBundle\Security\Voter\PostVoter;
use AdminBundle\Service\FileUploader;
use AdminBundle\Service\PostManager;
use AdminBundle\Service\RoleManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PostController extends Controller
{
    const PAGE_POSTS_LIMIT = 5;

    const DEFAULT_POSTS_PAGE_NUMBER = 1;

    /**
     *
     * @Route("/admin/posts/add", name="post_add")
     */
    public function addAction(Request $request, FileUploader $fileUploader, PostManager $postManager)
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if (false === $this->isGranted(PostVoter::ADD, $post)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $error = null;
            try {
                $postManager->addPost($post, $this->getUser(), $fileUploader);
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
     *
     * @Route("/admin/posts", name="admin_posts")
     */
    public function showAction(Request $request, PostManager $postManager)
    {
        if (false === $this->isGranted(PageVoter::POSTS)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $isAdmin = $this->isGranted(RoleManager::ROLE_ADMIN);
        $posts = $postManager->getPosts($isAdmin, $this->getUser()->getId());

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $posts,
            $request->query->getInt('page', self::DEFAULT_POSTS_PAGE_NUMBER),
            self::PAGE_POSTS_LIMIT
        );

        return $this->render('@Admin\Post\show.html.twig', array(
            'pagination' => $pagination,
        ));
    }

    /**
     *
     * @Route("/admin/posts/show/{id}", name="show_post")
     */    
    public function showPostAction($id, PostManager $postManager)
    {
        $post = $postManager->getPost($id);
        if (!$post) {
            throw $this->createNotFoundException();
        }

        if (false === $this->isGranted(PostVoter::VIEW, $post)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        return $this->render('@Admin\Post\post.html.twig',[
            'post' => $post
        ]);
    }
    
    /**
     *
     * @Route("/admin/posts/delete/{id}", name="delete_post")
     */
    public function deleteAction($id, PostManager $postManager)
    {
        $post = $postManager->getPost($id);

        if (!$post) {
            throw $this->createNotFoundException();
        }

        if (false === $this->isGranted(PostVoter::DELETE, $post)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $postManager->deletePost($post);
        return $this->redirectToRoute('admin_posts');
    }

    /**
     *
     * @Route("/admin/posts/edit/{id}", name="edit_post")
     */
    public function editAction(
        Request $request,
        $id,
        PostManager $postManager,
        FileUploader $fileUploader
    ) {
        $post = $postManager->getPost($id);
        if (!$post) {
            throw $this->createNotFoundException();
        }

        if (false === $this->isGranted(PostVoter::EDIT, $post)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $fileName = $post->getImageFile();
        $error = null;
        
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            try {
                $postManager->editPost($post, $fileName, $fileUploader);
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
