<?php

namespace Tests\Functional;

class AsLoginTest extends BaseTest
{
    public function testLoginOk()
    {
        $client = self::createClient();
        $client->request('POST', '/login', [
            'json' => '{"email": "m@b.com.ar", "password": "123"}',
        ]);

        $result = $client->getResponse()->getContent();
        self::$bearer = substr($result, 1, -1);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains('ey', $result);
        $this->assertNotContains('error', $result);
    }

    public function testLoginHashOk()
    {
        $client = self::createClient();
        $client->request('POST', '/login', [
            'json' => '{"email": "m@b.com.ar", "password": "123", "getHash": 1}',
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains('sub', $client->getResponse()->getContent());
        $this->assertContains('email', $client->getResponse()->getContent());
        $this->assertContains('name', $client->getResponse()->getContent());
        $this->assertContains('surname', $client->getResponse()->getContent());
        $this->assertContains('iat', $client->getResponse()->getContent());
        $this->assertContains('exp', $client->getResponse()->getContent());
        $this->assertNotContains('error', $client->getResponse()->getContent());
    }

    public function testLoginError()
    {
        $client = self::createClient();
        $client->request('POST', '/login', [
            'json' => '{"email": "", "password": ""}',
        ]);

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
        $this->assertContains('error', $client->getResponse()->getContent());
        $this->assertContains('incorrecto', $client->getResponse()->getContent());
        $this->assertNotContains('ey', $client->getResponse()->getContent());
    }
}
