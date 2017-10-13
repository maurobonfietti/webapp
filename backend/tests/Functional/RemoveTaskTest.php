<?php

namespace Tests\Functional;

class RemoveTaskTest extends BaseTest
{
    public function testRemoveTaskOk()
    {
        $client = self::createClient();
        $client->request('POST', '/task/remove/' . self::$id, [
            'authorization' => $this->getAuthToken(),
            'json' => '{"title":"test.", "description":"Mi test 1...", "status":"todo"}',
        ]);

        $this->assertEquals(204, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertNotContains('error', $client->getResponse()->getContent());
        $this->assertNotContains('Sin Autorizacion', $client->getResponse()->getContent());
    }

    public function testRemoveTaskError()
    {
        $client = self::createClient();
        $client->request('POST', '/task/remove/200');

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
        $this->assertContains('Sin Autorizacion', $client->getResponse()->getContent());
        $this->assertNotContains('success', $client->getResponse()->getContent());
    }
}
