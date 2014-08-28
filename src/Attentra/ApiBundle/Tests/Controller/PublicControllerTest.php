<?php

namespace Attentra\ApiBundle\Tests\Controller;

use Attentra\CoreBundle\Tests\Controller\RestWebTestCase;

/**
 * Permit to test the permit API functionnalities and the global REST API conf : are json and xml format usable ?
 */
class PublicControllerTest extends RestWebTestCase
{

    public function testBrowserPing()
    {
        $client = $this->createClient();
        $client->request('GET', '/api/public/ping');

        //Test if result is the same as with XML extension
        $clientExtension = $this->createClient();
        $clientExtension->request('GET', '/api/public/ping.xml');
        $this->assertXmlStringEqualsXmlString($clientExtension->getResponse()->getContent(), $client->getResponse()->getContent());
    }

    public function testXmlPing()
    {
        //Test with Accept header
        $client  = $this->createClient(array(), array('HTTP_ACCEPT' => 'application/xml'));
        $crawler = $client->request('GET', '/api/public/ping');
        $this->assertEquals($crawler->filterXPath('//result/entry')->text(), 'true');

        //Test with extension
        $clientExtension = $this->createClient();
        $clientExtension->request('GET', '/api/public/ping.xml');
        $this->assertXmlStringEqualsXmlString($clientExtension->getResponse()->getContent(), $client->getResponse()->getContent());
    }

    public function testJsonPing()
    {
        //Test with Accept header
        $response = $this->sendJsonRequest('GET', '/api/public/ping');
        $decoded  = $this->assertAndDecodeJsonResponse($response, 200);

        $this->assertEqualsAndSet(true, $decoded['ping']);

        //Test with extension
        $clientExtension = $this->createClient();
        $clientExtension->request('GET', '/api/public/ping.json');
        $this->assertJsonStringEqualsJsonString($clientExtension->getResponse()->getContent(), $response->getContent());
    }
}
