<?php

namespace Tests\Functional;

class EditUserTest extends BaseTest
{
    public function testEditUserOk()
    {
        $data = [
            'authorization' => $this->getAuthToken(),
            'json' => '{"name":"Mau","surname":"B","email": "m@b.com.ar", "password": "123"}',
        ];
        $client = self::createClient();
        $client->request('POST', '/user/edit', $data);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('success', $client->getResponse()->getContent());
        $this->assertContains('Usuario actualizado.', $client->getResponse()->getContent());
        $this->assertNotContains('error', $client->getResponse()->getContent());
        $this->assertNotContains('Authorization Invalid', $client->getResponse()->getContent());
    }

    public function testEditUserError()
    {
        $client = self::createClient();
        $client->request('POST', '/user/edit');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
        $this->assertContains('Authorization Invalid', $client->getResponse()->getContent());
        $this->assertNotContains('success', $client->getResponse()->getContent());
    }

    public function testEditUserNotEdited()
    {
        $data = [
            'authorization' => $this->getAuthToken(),
        ];
        $client = self::createClient();
        $client->request('POST', '/user/edit', $data);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertContains('Usuario no actualizado.', $client->getResponse()->getContent());
        $this->assertNotContains('success', $client->getResponse()->getContent());
    }

    public function testEditUserExists()
    {
        $data = [
            'authorization' => $this->getAuthToken(),
            'json' => '{"name":"Mau","surname":"B","email": "test@test.com", "password": "123"}',
        ];
        $client = self::createClient();
        $client->request('POST', '/user/edit', $data);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertContains('Usuario existente.', $client->getResponse()->getContent());
        $this->assertNotContains('success', $client->getResponse()->getContent());
    }
}
