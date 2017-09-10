<?php

//namespace BackendBundle\Tests\Controller;

//use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

//class DefaultControllerTest extends WebTestCase
//{
//    public function testIndex()
//    {
//        $client = static::createClient();
//
//        $crawler = $client->request('GET', '/');
//
//        $this->assertContains('Hello World', $client->getResponse()->getContent());
//    }

//    public function testIndex2()
//    {
//        $client = static::createClient();

//        $crawler = $client->request('POST', '/task/list');

//        var_dump($client->getResponse()->getStatusCode());
//        var_dump($client->getResponse());
//        var_dump($crawler->filter()->text());
//        exit;

//        $this->assertEquals(200, $client->getResponse()->getStatusCode());
//        $this->assertContains('Authorization Invalid', $crawler->filter('')->text());
//    }
//}
