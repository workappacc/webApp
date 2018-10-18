<?php

namespace FrontBundle\Tests\Service;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Query;
use FrontBundle\Entity\FeedBack;
use PHPUnit_Framework_Error_Notice;
use PHPUnit_Framework_Error_Warning;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FeedBackManagerTest extends WebTestCase
{
    private $client;
    private $container;
    private $service;

    public function assertPreConditions()
    {
        $this->client = $client = static::createClient();
        //start the symfony kernel
        $kernel = static::createKernel();
        $kernel->boot();

        //get the DI container
        $this->container = $kernel->getContainer();
        $this->service = $this->container->get('FrontBundle\Service\FeedBackManager');
    }

    public function testGetAllFeedBacks()
    {
        $query = $this->service->getAllFeedBacks();
        $this->assertTrue(($query instanceof Query));
        
        $array = $query->getArrayResult();
        $this->assertTrue(is_array($array));
    }

    public function testGetFeedBack()
    {
        $query = $this->service->getAllFeedBacks();
        $array = $query->getArrayResult();
        
        if (count($array)) {
            $query = $this->service->getFeedBack($array[0]['id']);
            $this->assertTrue(($query instanceof FeedBack));
        } 
    }

    public function testSaveFeedBack()
    {
        $feedBack = new FeedBack();
        $feedBack->setName('Test');
        $feedBack->setContent('<p id="test-feedback">some content</p>');
        $feedBack->setEmail('test@test.com');

        
        $this->service->saveFeedBack($feedBack);
        $id = $feedBack->getId();
        $this->assertTrue(is_int($id));
        
        $this->container->get('doctrine.orm.entity_manager')->remove($feedBack);
        $this->container->get('doctrine.orm.entity_manager')->flush($feedBack);
        
        $result = $this->service->getFeedBack($id);
        $this->assertTrue(($result === null));
    }
}
