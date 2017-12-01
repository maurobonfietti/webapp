<?php

namespace Tests\Functional;

class UpdateTaskTest extends BaseTest
{
    public function testUpdateTaskOk()
    {
        $client = self::createClient();
        $client->request('PATCH', '/task/edit/773', [
            'json' => '{"name":"Mau","surname":"B","email": "m@b.com.ar", "password": "123", "title":"test.", "description":"Mi test 1...", "status":"todo"}',
        ], [], ['HTTP_authorization' => $this->getAuthToken()]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains('success', $client->getResponse()->getContent());
        $this->assertNotContains('error', $client->getResponse()->getContent());
        $this->assertNotContains('Sin Autorizacion', $client->getResponse()->getContent());
    }

    public function testUpdateTaskError()
    {
        $client = self::createClient();
        $client->request('PATCH', '/task/edit/773');

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
        $this->assertContains('Sin Autorizacion', $client->getResponse()->getContent());
        $this->assertNotContains('success', $client->getResponse()->getContent());
    }

    public function testUpdateTaskNotFound()
    {
        $client = self::createClient();
        $client->request('PATCH', '/task/edit/1234567890', [
            'json' => '{"name":"Mau","surname":"B","email": "m@b.com.ar", "password": "123", "title":"test.", "description":"Mi test 1...", "status":"todo"}',
        ], [], ['HTTP_authorization' => $this->getAuthToken()]);

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        $this->assertContains('error', $client->getResponse()->getContent());
        $this->assertContains('Task not found', $client->getResponse()->getContent());
        $this->assertNotContains('success', $client->getResponse()->getContent());
    }
}
