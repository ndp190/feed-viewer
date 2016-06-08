<?php

namespace FeedBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(
            1,
            $crawler->filter('table')->count()
        );
        $this->assertEquals(
            2,
            $crawler->filter('thead > tr > th')->count()
        );
    }
}
