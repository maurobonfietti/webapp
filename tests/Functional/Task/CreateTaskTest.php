<?php

namespace Tests\Functional;

class CreateTaskTest extends BaseTest
{
    public function testCreateTaskOk()
    {
        $client = self::createClient();
        $client->request('POST', '/task/new', [
            'json' => '{"title":"test.", "description":"Mi test 1...", "status":"todo"}',
        ], [], ['HTTP_authorization' => $this->getAuthToken()]);

        $result = json_decode($client->getResponse()->getContent());
        self::$id = $result->task->id;

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains('success', $client->getResponse()->getContent());
        $this->assertContains('Tarea creada.', $client->getResponse()->getContent());
        $this->assertNotContains('error', $client->getResponse()->getContent());
        $this->assertNotContains('not authorized', $client->getResponse()->getContent());
    }

    public function testCreateTaskError()
    {
        $client = self::createClient();
        $client->request('POST', '/task/new');

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
        $this->assertContains('error', $client->getResponse()->getContent());
        $this->assertContains('not authorized', $client->getResponse()->getContent());
        $this->assertNotContains('success', $client->getResponse()->getContent());
    }

    public function testCreateTaskWithoutTitle()
    {
        $client = self::createClient();
        $client->request('POST', '/task/new', [
            'json' => '{"title":"", "description":"Mi test 1...", "status":"todo"}',
        ], [], ['HTTP_authorization' => $this->getAuthToken()]);

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertContains('error', $client->getResponse()->getContent());
        $this->assertContains('Los datos de la tarea no son validos', $client->getResponse()->getContent());
        $this->assertNotContains('success', $client->getResponse()->getContent());
    }

    public function testCreateTaskWithoutData()
    {
        $client = self::createClient();
        $client->request('POST', '/task/new', [], [], ['HTTP_authorization' => $this->getAuthToken()]);

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertContains('error', $client->getResponse()->getContent());
        $this->assertContains('Sin datos para actualizar la tarea', $client->getResponse()->getContent());
        $this->assertNotContains('success', $client->getResponse()->getContent());
    }
}
