<?php

namespace FrontBundle\Tests\Controller;

use PHPUnit_Framework_Error_Notice;
use PHPUnit_Framework_Error_Warning;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $client->request('GET', '/contact');

        $this->assertContains('_name', $client->getResponse()->getContent());
    }
}
