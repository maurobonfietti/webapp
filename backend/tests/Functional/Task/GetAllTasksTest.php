<?php

namespace Tests\Functional;

class GetAllTasksTest extends BaseTest
{
    public function testGetAllTasksOk()
    {
        $client = self::createClient();
        $client->request('GET', '/task/list', [], [], [
            'HTTP_authorization' => $this->getAuthToken(),
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains('success', $client->getResponse()->getContent());
        $this->assertNotContains('error', $client->getResponse()->getContent());
        $this->assertNotContains('not authorized', $client->getResponse()->getContent());
    }

    public function testGetAllTasksError()
    {
        $client = self::createClient();
        $client->request('GET', '/task/list');

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
        $this->assertContains('not authorized', $client->getResponse()->getContent());
        $this->assertNotContains('success', $client->getResponse()->getContent());
    }
}
