<?php

namespace FrontBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use FrontBundle\Entity\FeedBack;

class FeedBackManager
{
    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->em = $entityManagerInterface;
    }

    public function saveFeedBack(FeedBack $feedBack)
    {
        $this->em->persist($feedBack);
        $this->em->flush();
    }
    
    public function getAllFeedBacks()
    {
        return $this->em->getRepository(FeedBack::class)->getFeedBacks();
    }
    
    public function getFeedBack($id)
    {
        return $this->em->getRepository(FeedBack::class)->find($id);
    }

}