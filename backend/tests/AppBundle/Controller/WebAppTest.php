<?php

namespace Tests\AppBundle;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApplicationAvailabilityFunctionalTest extends WebTestCase
{
    /**
     * @return array
     */
    private function getAuthToken() {
        return [
            'authorization' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEzLCJlbWFpbCI6Im1AYi5jb20uYXIiLCJuYW1lIjoiTWF1cml0byIsInN1cm5hbWUiOiJCb25kIiwiaWF0IjoxNTA1MDc3NDQ2LCJleHAiOjE1MDU2ODIyNDZ9.VP6hyBPMCyzcYg5wnlQPVaFi85xjMo3un9etU4NETPY',
        ];
    }

    /**
     * Url of endpoints to test.
     *
     * @return array
     */
    public function urlProvider()
    {
        return array(
            array('/task/list'),
            array('/task/detail/18'),
        );
    }

    /**
     * @dataProvider urlProvider
     */
    public function testPageIsSuccessful($url)
    {
        $client = self::createClient();
        $client->request('POST', $url, $this->getAuthToken());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('success', $client->getResponse()->getContent());
        $this->assertContains('task', $client->getResponse()->getContent());
        $this->assertNotContains('error', $client->getResponse()->getContent());
        $this->assertNotContains('Authorization Invalid', $client->getResponse()->getContent());
    }

    /**
     * @dataProvider urlProvider
     */
    public function testPageIsNotAllowed($url)
    {
        $client = self::createClient();
        $client->request('POST', $url);
        $this->assertContains('Authorization Invalid', $client->getResponse()->getContent());
        $this->assertContains('error', $client->getResponse()->getContent());
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
        $this->assertNotContains('success', $client->getResponse()->getContent());
    }

    public function testTaskNotFound()
    {
        $client = self::createClient();
        $client->request('POST', '/task/detail/1', $this->getAuthToken());
        $this->assertContains('Task not found', $client->getResponse()->getContent());
        $this->assertContains('error', $client->getResponse()->getContent());
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        $this->assertNotContains('success', $client->getResponse()->getContent());
    }
}
