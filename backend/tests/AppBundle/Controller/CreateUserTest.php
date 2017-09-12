<?php

namespace Tests\AppBundle;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CreateUserTest extends WebTestCase
{
    public function testCreateUserOk()
    {
        $rnd = rand(1, 99999);
        $data = [
            'json' => '{"name":"test","surname":"test","email": "test-'.$rnd.'@test.com", "password": "test"}',
        ];
        $client = self::createClient();
        $client->request('POST', '/user/new', $data);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('User Created', $client->getResponse()->getContent());
        $this->assertNotContains('error', $client->getResponse()->getContent());
    }

    public function testCreateUserError()
    {
        $data = [
            'json' => '{"email": "", "password": ""}',
        ];
        $client = self::createClient();
        $client->request('POST', '/user/new', $data);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('User Not Created', $client->getResponse()->getContent());
        $this->assertContains('error', $client->getResponse()->getContent());
        $this->assertNotContains('success', $client->getResponse()->getContent());
    }

    public function testCreateUserExists()
    {
        $data = [
            'json' => '{"name":"test","surname":"test","email": "test@test.com", "password": "test"}',
        ];
        $client = self::createClient();
        $client->request('POST', '/user/new', $data);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('User exists', $client->getResponse()->getContent());
        $this->assertContains('error', $client->getResponse()->getContent());
        $this->assertNotContains('success', $client->getResponse()->getContent());
    }
}
