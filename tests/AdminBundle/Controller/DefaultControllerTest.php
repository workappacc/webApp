<?php

namespace AdminBundle\Tests\Controller;

use PHPUnit_Framework_Error_Notice;
use PHPUnit_Framework_Error_Warning;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class DefaultControllerTest extends WebTestCase
{
    private $client = null;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function testDefault()
    {
        $this->client->request('GET', '/admin');
        $this->assertTrue($this->client->getResponse()->isRedirect());
    }
}