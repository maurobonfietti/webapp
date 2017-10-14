<?php

namespace Tests\Functional;

class RemoveTaskTest extends BaseTest
{
    public function testRemoveTaskOk()
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

    public function testRemoveTaskError()
    {
        $client = self::createClient();
        $client->request('DELETE', '/task/remove/200');

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
        $this->assertContains('Sin Autorizacion', $client->getResponse()->getContent());
        $this->assertNotContains('success', $client->getResponse()->getContent());
    }
}
