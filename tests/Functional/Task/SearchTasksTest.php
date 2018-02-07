<?php

namespace Tests\Functional;

class SearchTasksTest extends BaseTest
{
    public function testSearchTasksOrder()
    {
        $client = self::createClient();
        $client->request('GET', '/task/search', [
            'authorization' => $this->getAuthToken(),
            'order' => '1',
        ], [], ['HTTP_authorization' => $this->getAuthToken()]);

        $result = $client->getResponse()->getContent();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains('success', $result);
        $this->assertNotContains('not authorized', $result);
    }

    public function testSearchTasksFilter2()
    {
        $client = self::createClient();
        $client->request('GET', '/task/search', [
            'authorization' => $this->getAuthToken(),
            'filter' => '2',
        ], [], ['HTTP_authorization' => $this->getAuthToken()]);

        $result = $client->getResponse()->getContent();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains('success', $result);
        $this->assertContains('todo', $result);
        $this->assertNotContains('error', $result);
        $this->assertNotContains('not authorized', $result);
    }

    public function testSearchTasksFilter3()
    {
        $client = self::createClient();
        $client->request('GET', '/task/search', [
            'authorization' => $this->getAuthToken(),
            'filter' => '3',
        ], [], ['HTTP_authorization' => $this->getAuthToken()]);

        $result = $client->getResponse()->getContent();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains('success', $result);
        $this->assertContains('finished', $result);
        $this->assertNotContains('not authorized', $result);
    }

    public function testSearchTasks()
    {
        $client = self::createClient();
        $client->request('GET', '/task/search/t', [
            'authorization' => $this->getAuthToken(),
        ], [], ['HTTP_authorization' => $this->getAuthToken()]);

        $result = $client->getResponse()->getContent();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains('success', $result);
        $this->assertNotContains('not authorized', $result);
    }

    public function testSearchTasksError()
    {
        $client = self::createClient();
        $client->request('GET', '/task/search');

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
        $this->assertContains('error', $client->getResponse()->getContent());
        $this->assertContains('not authorized', $client->getResponse()->getContent());
        $this->assertNotContains('success', $client->getResponse()->getContent());
    }
}
