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
            array('/task/edit/41'),
        );
    }

    /**
     * @dataProvider urlProvider
     */
    public function testPageIsSuccessful($url)
    {
        $client = self::createClient();
        $client->request('POST', $url, [
            'authorization' => $this->getAuthToken(),
            'json' => '{"name":"Mau","surname":"B","email": "m@b.com.ar", "password": "123", "title":"test.", "description":"Mi test 1...", "status":"todo"}',
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains('success', $client->getResponse()->getContent());
        $this->assertNotContains('error', $client->getResponse()->getContent());
        $this->assertNotContains('Sin Autorizacion', $client->getResponse()->getContent());
    }

    /**
     * @dataProvider urlProvider
     */
    public function testPageIsNotAllowed($url)
    {
        $client = self::createClient();
        $client->request('POST', $url);

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
        $this->assertContains('Sin Autorizacion', $client->getResponse()->getContent());
        $this->assertContains('error', $client->getResponse()->getContent());        
        $this->assertNotContains('success', $client->getResponse()->getContent());
    }

    public function testTaskNotFound()
    {
        $client = self::createClient();
        $client->request('POST', '/task/detail/1', [
            'authorization' => $this->getAuthToken(),
            'json' => '{"name":"Mau","surname":"B","email": "m@b.com.ar", "password": "123", "title":"test.", "description":"Mi test 1...", "status":"todo"}',
        ]);

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        $this->assertContains('Task not found', $client->getResponse()->getContent());
        $this->assertContains('error', $client->getResponse()->getContent());
        $this->assertNotContains('success', $client->getResponse()->getContent());
    }
}
