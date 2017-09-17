<?php

namespace Tests\AppBundle;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EditUserTest extends WebTestCase
{
    public function testEditUserOk()
    {
        $data = [
            'authorization' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEzLCJlbWFpbCI6Im1AYi5jb20uYXIiLCJuYW1lIjoiTWF1Iiwic3VybmFtZSI6IkIiLCJpYXQiOjE1MDU2ODM4MzQsImV4cCI6MTUwNjI4ODYzNH0.-l0r61i2pyC8u-EdiKSHJ14MkVOeq2Qo2t5kbXmBEZo',
            'json' => '{"name":"Mau","surname":"B","email": "m@b.com.ar", "password": "123"}',
        ];
        $client = self::createClient();
        $client->request('POST', '/user/edit', $data);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('success', $client->getResponse()->getContent());
        $this->assertContains('Usuario actualizado.', $client->getResponse()->getContent());
        $this->assertNotContains('error', $client->getResponse()->getContent());
        $this->assertNotContains('Authorization Invalid', $client->getResponse()->getContent());
    }

    public function testEditUserError()
    {
        $client = self::createClient();
        $client->request('POST', '/user/edit');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
        $this->assertContains('Authorization Invalid', $client->getResponse()->getContent());
        $this->assertNotContains('success', $client->getResponse()->getContent());
    }

    public function testEditUserNotEdited()
    {
        $data = [
            'authorization' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEzLCJlbWFpbCI6Im1AYi5jb20uYXIiLCJuYW1lIjoiTWF1Iiwic3VybmFtZSI6IkIiLCJpYXQiOjE1MDU2ODM4MzQsImV4cCI6MTUwNjI4ODYzNH0.-l0r61i2pyC8u-EdiKSHJ14MkVOeq2Qo2t5kbXmBEZo',
        ];
        $client = self::createClient();
        $client->request('POST', '/user/edit', $data);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertContains('Usuario no actualizado.', $client->getResponse()->getContent());
        $this->assertNotContains('success', $client->getResponse()->getContent());
    }

    public function testEditUserExists()
    {
        $data = [
            'authorization' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEzLCJlbWFpbCI6Im1AYi5jb20uYXIiLCJuYW1lIjoiTWF1Iiwic3VybmFtZSI6IkIiLCJpYXQiOjE1MDU2ODM4MzQsImV4cCI6MTUwNjI4ODYzNH0.-l0r61i2pyC8u-EdiKSHJ14MkVOeq2Qo2t5kbXmBEZo',
            'json' => '{"name":"Mau","surname":"B","email": "test@test.com", "password": "123"}',
        ];
        $client = self::createClient();
        $client->request('POST', '/user/edit', $data);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertContains('Usuario existente.', $client->getResponse()->getContent());
        $this->assertNotContains('success', $client->getResponse()->getContent());
    }
}
