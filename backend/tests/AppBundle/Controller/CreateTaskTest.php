<?php

namespace Tests\AppBundle;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CreateTaskTest extends WebTestCase
{
    public function testCreateTaskOk()
    {
        $data = [
            'authorization' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEzLCJlbWFpbCI6Im1AYi5jb20uYXIiLCJuYW1lIjoiTWF1Iiwic3VybmFtZSI6IkIiLCJpYXQiOjE1MDU2ODM4MzQsImV4cCI6MTUwNjI4ODYzNH0.-l0r61i2pyC8u-EdiKSHJ14MkVOeq2Qo2t5kbXmBEZo',
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
