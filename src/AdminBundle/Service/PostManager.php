<?php

namespace AdminBundle\Service;

use AdminBundle\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;

class PostManager
{
    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->em = $entityManagerInterface;
    }

    public function getPosts($isAdmin, $userId = 0)
    {
        if ($isAdmin) {
            $posts = $this->em->getRepository(Post::class)->getAllPosts();
        } else {
            $posts = $this->em->getRepository(Post::class)->getUserPosts($userId);
        }
        
        return $posts;
    }
    
    public function addPost(Post $post, $user, FileUploader $fileUploader)
    {
        $file = $post->getImageFile();
        $fileName = '';
        if ($file) {
            $fileUploader->setUploadedFile($file);
            $fileUploader->validateImage();
            $fileName = $fileUploader->upload();
        }

        $post->setTitle($post->getTitle());
        $post->setUser($user);
        $post->setContent($post->getContent());
        $post->setImage($fileName);
        $post->setCreatedAt(new \DateTime());
        $post->setUpdatedAt(null);
        $post->setStatus($post->getStatus());

        $this->em->persist($post);
        $this->em->flush();
    }
    
    public function getPost($id, $isAdmin, $userId = 0)
    {
        if ($isAdmin) {
            return $this->em->getRepository(Post::class)->getPost($id);    
        } else {
            return $this->em->getRepository(Post::class)->getUserPost($id, $userId);    
        }
        
    }

    public function deletePost($id)
    {
        $post = $this->em->getRepository(Post::class)->find($id);
        $this->em->remove($post);
        $this->em->flush();
    }

    public function editPost(Post $post, $fileName, FileUploader $fileUploader)
    {
        $newFile = $post->getImageFile();

        $post->setTitle($post->getTitle());
        $post->setContent($post->getContent());
        $post->setStatus($post->getStatus());
        $post->setUpdatedAt(new \DateTime());

        if ($newFile) {
            $fileUploader->setUploadedFile($newFile);
            $fileUploader->validateImage();
            $fileName = $fileUploader->upload();
        }

        $post->setImage($fileName);

        $this->em->persist($post);
        $this->em->flush();
    }
    
    public function getPublishedPosts()
    {
        return $this->em->getRepository(Post::class)->getPublishedPosts();
    }
    
    public function getPublishedPost($id)
    {
        return $this->em->getRepository(Post::class)->getPublishedPost($id);
    }
}