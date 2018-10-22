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

        $post->setUser($user);
        $post->setImage($fileName);
        $post->setCreatedAt(new \DateTime());

        $this->em->persist($post);
        $this->em->flush();
    }
    
    public function getPost($id)
    {
        return $this->em->getRepository(Post::class)->getPost($id);
    }

    public function deletePost(Post $post)
    {
        $this->em->remove($post);
        $this->em->flush();
    }

    public function editPost(Post $post, $fileName, FileUploader $fileUploader)
    {
        $newFile = $post->getImageFile();
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