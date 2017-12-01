<?php

namespace Tests\Functional;

class CreateUserTest extends BaseTest
{
    public function testCreateUserOk()
    {
        $client = self::createClient();
        $client->request('POST', '/user/new', [
            'json' => '{"name":"test","surname":"test","email": "test-'.rand(1, 99999).'@test.com", "password": "test"}',
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains('The user was created.', $client->getResponse()->getContent());
        $this->assertNotContains('error', $client->getResponse()->getContent());
    }

    public function testCreateUserError()
    {
        $client = self::createClient();
        $client->request('POST', '/user/new', [
            'json' => '{"email": "", "password": ""}',
        ]);

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertContains('The user was NOT created.', $client->getResponse()->getContent());
        $this->assertContains('error', $client->getResponse()->getContent());
        $this->assertNotContains('success', $client->getResponse()->getContent());
    }

    public function testCreateUserExists()
    {
        $client = self::createClient();
        $client->request('POST', '/user/new', [
            'json' => '{"name":"test","surname":"test","email": "test@test.com", "password": "test"}',
        ]);

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertContains('The user already exists.', $client->getResponse()->getContent());
        $this->assertContains('error', $client->getResponse()->getContent());
        $this->assertNotContains('success', $client->getResponse()->getContent());
    }
}
