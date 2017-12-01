<?php

namespace Tests\Functional;

class DeleteTaskTest extends BaseTest
{
    public function testDeleteTaskOk()
    {
        $client = self::createClient();
        $client->request('DELETE', '/task/remove/' . self::$id, [], [], [
            'HTTP_authorization' => $this->getAuthToken(),
        ]);

        $this->assertEquals(204, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertNotContains('error', $client->getResponse()->getContent());
        $this->assertNotContains('Sin Autorizacion', $client->getResponse()->getContent());
    }

    public function testDeleteTaskError()
    {
        $client = self::createClient();
        $client->request('DELETE', '/task/remove/200');

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
        $this->assertContains('Sin Autorizacion', $client->getResponse()->getContent());
        $this->assertNotContains('success', $client->getResponse()->getContent());
    }

    public function testDeleteTaskNotFound()
    {
        $client = self::createClient();
        $client->request('DELETE', '/task/remove/1234567890', [], [], [
            'HTTP_authorization' => $this->getAuthToken(),
        ]);

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        $this->assertContains('Task not found', $client->getResponse()->getContent());
        $this->assertNotContains('success', $client->getResponse()->getContent());
    }
}
