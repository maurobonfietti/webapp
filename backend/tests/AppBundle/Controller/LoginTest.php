<?php

namespace Tests\AppBundle;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginTest extends WebTestCase
{
    public function testLoginOk()
    {
        $data = [
            'json' => '{"email": "m@b.com.ar", "password": "123"}',
        ];
        $client = self::createClient();
        $client->request('POST', '/login', $data);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('ey', $client->getResponse()->getContent());
        $this->assertNotContains('error', $client->getResponse()->getContent());
    }

    public function testLoginHashOk()
    {
        $data = [
            'json' => '{"email": "m@b.com.ar", "password": "123", "getHash": 1}',
        ];
        $client = self::createClient();
        $client->request('POST', '/login', $data);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
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
        $data = [
            'json' => '{"email": "", "password": ""}',
        ];
        $client = self::createClient();
        $client->request('POST', '/login', $data);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('incorrecto', $client->getResponse()->getContent());
        $this->assertNotContains('ey', $client->getResponse()->getContent());
    }
}
