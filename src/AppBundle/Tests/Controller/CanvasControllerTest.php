<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CanvasControllerTest extends WebTestCase
{
    public function testShowcanvas()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/showCanvas');
    }

}
