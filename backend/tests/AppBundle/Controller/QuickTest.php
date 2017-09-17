<?php

namespace Tests\AppBundle;

class QuickTest extends BaseTest
{
    /**
     * Url of endpoints to test.
     *
     * @return array
     */
    public function urlProvider()
    {
        return array(
            array('/task/search'),
//            array('/task/remove/40'),
        );
    }

    /**
     * @dataProvider urlProvider
     */
    public function testPageIsSuccessful($url)
    {
        $data = [
            'authorization' => $this->getAuthToken(),
        ];
        $client = self::createClient();
        $client->request('POST', $url, $data);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('success', $client->getResponse()->getContent());
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
        $this->assertContains('400', $client->getResponse()->getContent());
        $this->assertNotContains('success', $client->getResponse()->getContent());
    }
}
