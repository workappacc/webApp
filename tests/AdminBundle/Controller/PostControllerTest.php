<?php

namespace AdminBundle\Tests\Controller;

use PHPUnit_Framework_Error_Notice;
use PHPUnit_Framework_Error_Warning;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class PostControllerTest extends WebTestCase
{
    private $client = null;

    private $container;
    


    public function assertPreConditions()
    {
        //start the symfony kernel
        $kernel = static::createKernel();
        $kernel->boot();

        //get the DI container
        $this->container = $kernel->getContainer();
        
    }
    
    public function setUp()
    {
        $this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'admin',
        ));
    }

    public function testShowAction()
    {
        $crawler = $this->client->request('GET', '/admin/posts', array(), array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'admin',
        ));

        $this->assertSame('Admin Dashboard', $crawler->filter('h4')->text());
    }

    public function testShowPostAction()
    {
        $this->client->request('GET', '/admin/posts/show/non-exists-post-id', array(), array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'admin',
        ));
        $this->assertTrue($this->client->getResponse()->isRedirect());

        $posts = $this->container->get('AdminBundle\Service\PostManager')->getPosts(true)->getArrayResult();
        
        if (count($posts)) {
            $postId = $posts[0]['id'];
            $this->client->request('GET', '/admin/posts/show/' . $postId, array(), array(), array(
                'PHP_AUTH_USER' => 'admin',
                'PHP_AUTH_PW'   => 'admin',
            ));
            $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        }
    }
}