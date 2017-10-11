<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WorkSpacesControllerTest extends WebTestCase
{
    public function testShowlist()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/showList');
    }

}
