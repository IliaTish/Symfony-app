<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WorkSpaceManagerControllerTest extends WebTestCase
{
    public function testShowmanager()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/showManager');
    }

}
