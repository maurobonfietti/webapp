<?php

namespace Tests\AppBundle;

class CreateUserTest extends BaseTest
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
        $this->assertContains('Usuario creado.', $client->getResponse()->getContent());
        $this->assertNotContains('error', $client->getResponse()->getContent());
    }

    public function testCreateUserError()
    {
        $data = [
            'json' => '{"email": "", "password": ""}',
        ];
        $client = self::createClient();
        $client->request('POST', '/user/new', $data);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertContains('Usuario no creado.', $client->getResponse()->getContent());
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
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertContains('Usuario existente.', $client->getResponse()->getContent());
        $this->assertContains('error', $client->getResponse()->getContent());
        $this->assertNotContains('success', $client->getResponse()->getContent());
    }
}
