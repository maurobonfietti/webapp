<?php

namespace Tests\Functional;

class EditUserTest extends BaseTest
{
    public function testEditUserOk()
    {
        $client = self::createClient();
        $client->request('POST', '/user/edit', [
            'authorization' => $this->getAuthToken(),
            'json' => '{"name":"Mau","surname":"B","email": "m@b.com.ar", "password": "123"}',
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains('success', $client->getResponse()->getContent());
        $this->assertContains('Usuario actualizado.', $client->getResponse()->getContent());
        $this->assertNotContains('error', $client->getResponse()->getContent());
        $this->assertNotContains('Sin Autorizacion', $client->getResponse()->getContent());
    }

    public function testEditUserError()
    {
        $client = self::createClient();
        $client->request('POST', '/user/edit');

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
        $this->assertContains('Sin Autorizacion', $client->getResponse()->getContent());
        $this->assertNotContains('success', $client->getResponse()->getContent());
    }

    public function testEditUserNotEdited()
    {
        $client = self::createClient();
        $client->request('POST', '/user/edit', [
            'authorization' => $this->getAuthToken(),
        ]);

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertContains('Usuario no actualizado.', $client->getResponse()->getContent());
        $this->assertNotContains('success', $client->getResponse()->getContent());
    }

    public function testEditUserExists()
    {
        $client = self::createClient();
        $client->request('POST', '/user/edit', [
            'authorization' => $this->getAuthToken(),
            'json' => '{"name":"Mau","surname":"B","email": "test@test.com", "password": "123"}',
        ]);

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertContains('Usuario existente.', $client->getResponse()->getContent());
        $this->assertNotContains('success', $client->getResponse()->getContent());
    }
}
