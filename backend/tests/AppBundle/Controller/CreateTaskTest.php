<?php

namespace Tests\AppBundle;

class CreateTaskTest extends BaseTest
{
    public function testCreateTaskOk()
    {
        $data = [
            'authorization' => $this->getAuthToken(),
            'json' => '{"title":"test.", "description":"Mi test 1...", "status":"todo"}',
        ];
        $client = self::createClient();
        $client->request('POST', '/task/new', $data);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('success', $client->getResponse()->getContent());
        $this->assertContains('Task Created.', $client->getResponse()->getContent());
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
