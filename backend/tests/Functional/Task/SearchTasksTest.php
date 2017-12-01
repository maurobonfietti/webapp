<?php

namespace Tests\Functional;

class SearchTasksTest extends BaseTest
{
    public function testSearchTasksOrder()
    {
        $client = self::createClient();
        $client->request('POST', '/task/search', [
            'authorization' => $this->getAuthToken(),
            'order' => '1',
        ]);

        $result = $client->getResponse()->getContent();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains('success', $result);
        $this->assertNotContains('error', $result);
        $this->assertNotContains('not authorized', $result);
    }

    public function testSearchTasksFilter1()
    {
        $client = self::createClient();
        $client->request('POST', '/task/search', [
            'authorization' => $this->getAuthToken(),
            'filter' => '1',
        ]);

        $result = $client->getResponse()->getContent();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains('success', $result);
        $this->assertContains('new', $result);
        $this->assertNotContains('error', $result);
        $this->assertNotContains('not authorized', $result);
    }

    public function testSearchTasksFilter2()
    {
        $client = self::createClient();
        $client->request('POST', '/task/search', [
            'authorization' => $this->getAuthToken(),
            'filter' => '2',
        ]);

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
        $client->request('POST', '/task/search', [
            'authorization' => $this->getAuthToken(),
            'filter' => '3',
        ]);

        $result = $client->getResponse()->getContent();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains('success', $result);
        $this->assertContains('finished', $result);
        $this->assertNotContains('error', $result);
        $this->assertNotContains('not authorized', $result);
    }

    public function testSearchTasks()
    {
        $client = self::createClient();
        $client->request('POST', '/task/search/t', [
            'authorization' => $this->getAuthToken(),
        ]);

        $result = $client->getResponse()->getContent();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains('success', $result);
        $this->assertNotContains('error', $result);
        $this->assertNotContains('not authorized', $result);
    }

    public function testSearchTasksError()
    {
        $client = self::createClient();
        $client->request('POST', '/task/search');

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
        $this->assertContains('not authorized', $client->getResponse()->getContent());
        $this->assertNotContains('success', $client->getResponse()->getContent());
    }
}
