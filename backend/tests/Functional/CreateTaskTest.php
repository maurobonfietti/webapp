<?php

namespace Tests\Functional;

class CreateTaskTest extends BaseTest
{
    public function testCreateTaskOk()
    {
        $client = self::createClient();
        $client->request('POST', '/task/new', [
            'authorization' => $this->getAuthToken(),
            'json' => '{"title":"test.", "description":"Mi test 1...", "status":"todo"}',
        ]);

        $result = json_decode($client->getResponse()->getContent());
        self::$id = $result->task->id;

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains('success', $client->getResponse()->getContent());
        $this->assertContains('Tarea creada.', $client->getResponse()->getContent());
        $this->assertNotContains('error', $client->getResponse()->getContent());
        $this->assertNotContains('Authorization Invalid', $client->getResponse()->getContent());
    }

    public function testCreateTaskError()
    {
        $client = self::createClient();
        $client->request('POST', '/task/new');

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
        $this->assertContains('Authorization Invalid', $client->getResponse()->getContent());
        $this->assertNotContains('success', $client->getResponse()->getContent());
    }
}
