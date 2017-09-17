<?php

namespace Tests\AppBundle;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class QuickTest extends WebTestCase
{
    /**
     * @return array
     */
    private function getAuthToken()
    {
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
//            array('/task/edit/11'),
            array('/task/search'),
            array('/task/remove/11'),
//            array('/task/new'),
//            array('/user/edit'),
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
//        $this->assertContains('success', $client->getResponse()->getContent());
//        $this->assertContains('task', $client->getResponse()->getContent());
//        $this->assertNotContains('error', $client->getResponse()->getContent());
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
        $this->assertContains('400', $client->getResponse()->getContent());
        $this->assertNotContains('success', $client->getResponse()->getContent());
    }
}
