<?php

namespace Tests\Functional;

class RemoveTaskTest extends BaseTest
{
    public function testRemoveTaskOk()
    {
        $data = [
            'authorization' => $this->getAuthToken(),
            'json' => '{"title":"test.", "description":"Mi test 1...", "status":"todo"}',
        ];
        $client = self::createClient();
        $client->request('POST', '/task/remove/' . self::$id, $data);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('success', $client->getResponse()->getContent());
        $this->assertNotContains('error', $client->getResponse()->getContent());
        $this->assertNotContains('Authorization Invalid', $client->getResponse()->getContent());
    }

    public function testRemoveTaskError()
    {
        $client = self::createClient();
        $client->request('POST', '/task/remove/200');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
        $this->assertContains('Authorization Invalid', $client->getResponse()->getContent());
        $this->assertNotContains('success', $client->getResponse()->getContent());
    }
}
