<?php

namespace Tests\Functional;

class ApplicationAvailabilityFunctionalTest extends BaseTest
{
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
            array('/task/search'),
        );
    }

    /**
     * @dataProvider urlProvider
     */
    public function testPageIsSuccessful($url)
    {
        $data = [
            'authorization' => $this->getAuthToken(),
            'json' => '{"name":"Mau","surname":"B","email": "m@b.com.ar", "password": "123", "title":"test.", "description":"Mi test 1...", "status":"todo"}',
        ];
        $client = self::createClient();
        $client->request('POST', $url, $data);
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
        $data = [
            'authorization' => $this->getAuthToken(),
            'json' => '{"name":"Mau","surname":"B","email": "m@b.com.ar", "password": "123", "title":"test.", "description":"Mi test 1...", "status":"todo"}',
        ];
        $client = self::createClient();
        $client->request('POST', '/task/detail/1', $data);
        $this->assertContains('Task not found', $client->getResponse()->getContent());
        $this->assertContains('error', $client->getResponse()->getContent());
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        $this->assertNotContains('success', $client->getResponse()->getContent());
    }
}
