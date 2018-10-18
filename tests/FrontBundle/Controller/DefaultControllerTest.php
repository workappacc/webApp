<?php

namespace FrontBundle\Tests\Controller;

use PHPUnit_Framework_Error_Notice;
use PHPUnit_Framework_Error_Warning;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public $client;
    public $container;

    public function assertPreConditions()
    {
        $this->client = $client = static::createClient();
        //start the symfony kernel
        $kernel = static::createKernel();
        $kernel->boot();

        //get the DI container
        $this->container = $kernel->getContainer();
    }

    public function testIndex()
    {
        $this->client->request('GET', '/');
        $this->assertContains('Welcome, to application', $this->client->getResponse()->getContent());
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );
    }
    
    public function testShow()
    {
        $this->client->request('GET', '/show/non-exist-id');
        $this->assertTrue($this->client->getResponse()->isRedirect());
    }
    
    public function testShowPosts()
    {
        $array = $this->container->get('AdminBundle\Service\PostManager')->getPublishedPosts();
        $this->assertTrue(is_array($array));
    }
}
