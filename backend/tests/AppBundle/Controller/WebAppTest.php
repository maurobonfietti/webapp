<?php

namespace Tests\AppBundle;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApplicationAvailabilityFunctionalTest extends WebTestCase
{
    /**
     * @return array
     */
    private function getAuthToken()
    {
        return [
            'authorization' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEzLCJlbWFpbCI6Im1AYi5jb20uYXIiLCJuYW1lIjoiTWF1Iiwic3VybmFtZSI6IkIiLCJpYXQiOjE1MDU2ODM4MzQsImV4cCI6MTUwNjI4ODYzNH0.-l0r61i2pyC8u-EdiKSHJ14MkVOeq2Qo2t5kbXmBEZo',
            'json' => '{"name":"Mau","surname":"B","email": "m@b.com.ar", "password": "123", "title":"test.", "description":"Mi test 1...", "status":"todo"}',
        ];
    }

    /**
     * Url of endpoints to test.
     *
     * @return array
     */
    public function urlProvider()
    {
        return array(
            array('/task/list'),
            array('/task/detail/18'),
            array('/user/edit'),
            array('/task/new'),
            array('/task/edit/11'),
        );
    }

    /**
     * @dataProvider urlProvider
     */
    public function testPageIsSuccessful($url)
    {
        $client = self::createClient();
        $client->request('POST', $url, $this->getAuthToken());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('success', $client->getResponse()->getContent());
        $this->assertNotContains('error', $client->getResponse()->getContent());
        $this->assertNotContains('Authorization Invalid', $client->getResponse()->getContent());
    }

    /**
     * @dataProvider urlProvider
     */
    public function testPageIsNotAllowed($url)
    {
        $client = self::createClient();
        $client->request('POST', $url);
        $this->assertContains('Authorization Invalid', $client->getResponse()->getContent());
        $this->assertContains('error', $client->getResponse()->getContent());
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
        $this->assertNotContains('success', $client->getResponse()->getContent());
    }

    public function testTaskNotFound()
    {
        $client = self::createClient();
        $client->request('POST', '/task/detail/1', $this->getAuthToken());
        $this->assertContains('Task not found', $client->getResponse()->getContent());
        $this->assertContains('error', $client->getResponse()->getContent());
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        $this->assertNotContains('success', $client->getResponse()->getContent());
    }
}
