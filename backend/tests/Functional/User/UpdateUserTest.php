<?php

namespace Tests\Functional;

class UpdateUserTest extends BaseTest
{
    public function testUpdateUserOk()
    {
        $client = self::createClient();
        $client->request('PATCH', '/user/edit', [
            'json' => '{"name":"Mau","surname":"B","email": "m@b.com.ar", "password": "123"}',
        ], [], ['HTTP_authorization' => $this->getAuthToken()]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains('success', $client->getResponse()->getContent());
        $this->assertContains('The user was updated.', $client->getResponse()->getContent());
        $this->assertNotContains('error', $client->getResponse()->getContent());
        $this->assertNotContains('not authorized', $client->getResponse()->getContent());
    }

    public function testUpdateUserError()
    {
        $client = self::createClient();
        $client->request('PATCH', '/user/edit');

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
        $this->assertContains('error', $client->getResponse()->getContent());
        $this->assertContains('not authorized', $client->getResponse()->getContent());
        $this->assertNotContains('success', $client->getResponse()->getContent());
    }

    public function testUpdateUserNotEdited()
    {
        $client = self::createClient();
        $client->request('PATCH', '/user/edit', [], [], ['HTTP_authorization' => $this->getAuthToken()]);

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertContains('The user was NOT updated.', $client->getResponse()->getContent());
        $this->assertNotContains('success', $client->getResponse()->getContent());
    }

    public function testUpdateUserExists()
    {
        $client = self::createClient();
        $client->request('PATCH', '/user/edit', [
            'json' => '{"name":"Mau","surname":"B","email": "test@test.com", "password": "123"}',
        ], [], ['HTTP_authorization' => $this->getAuthToken()]);

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertContains('The user already exists.', $client->getResponse()->getContent());
        $this->assertNotContains('success', $client->getResponse()->getContent());
    }
}
