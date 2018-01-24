<?php

namespace Tests\Functional;

class DefaultTest extends BaseTest
{
    public function testStatusOk()
    {
        $client = self::createClient();
        $client->request('GET', '/status');
        $result = $client->getResponse()->getContent();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains('api', $result);
        $this->assertContains('version', $result);
        $this->assertContains('status', $result);
        $this->assertContains('OK', $result);
        $this->assertNotContains('error', $result);
    }

    public function testCheckOk()
    {
        $client = self::createClient();
        $client->request('GET', '/test');
        $result = $client->getResponse()->getContent();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains('api', $result);
        $this->assertContains('version', $result);
        $this->assertContains('status', $result);
        $this->assertContains('database', $result);
        $this->assertContains('user', $result);
        $this->assertContains('tasks', $result);
        $this->assertContains('OK', $result);
        $this->assertNotContains('error', $result);
    }
}
