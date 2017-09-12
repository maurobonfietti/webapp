<?php

namespace Tests\AppBundle;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PruebaTest extends WebTestCase
{
    private function getAuthToken() {
        return [
            'authorization' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEzLCJlbWFpbCI6Im1AYi5jb20uYXIiLCJuYW1lIjoiTWF1cml0byIsInN1cm5hbWUiOiJCb25kIiwiaWF0IjoxNTA1MDc3NDQ2LCJleHAiOjE1MDU2ODIyNDZ9.VP6hyBPMCyzcYg5wnlQPVaFi85xjMo3un9etU4NETPY',
        ];
    }

    public function testPruebaOk()
    {
        $client = self::createClient();
        $client->request('POST', '/pruebas', $this->getAuthToken());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('success', $client->getResponse()->getContent());
        $this->assertContains('users', $client->getResponse()->getContent());
        $this->assertNotContains('error', $client->getResponse()->getContent());
        $this->assertNotContains('Authorization Invalid', $client->getResponse()->getContent());
    }

    public function testPruebaError()
    {
        $client = self::createClient();
        $client->request('POST', '/pruebas');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Authorization Invalid', $client->getResponse()->getContent());
        $this->assertNotContains('success', $client->getResponse()->getContent());
    }
}
