<?php

namespace Attentra\CoreBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class RestWebTestCase extends WebTestCase
{
    /**
     * @param $expected
     * @param $actual
     * @param string $message
     */
    protected function assertEqualsAndSet($expected, &$actual, $message = '')
    {
        $this->assertTrue(isset($actual), 'Actual value not set.');
        $this->assertEquals($expected, $actual, $message);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param string $content
     * @return Response
     */
    protected function sendJsonRequest($method, $uri, $content = '')
    {
        $client = $this->createClient([], ['HTTP_CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json']);
        $client->request($method, $uri, [], [], ['CONTENT_TYPE' => 'application/json'], $content);
        return $client->getResponse();
    }

    /**
     * @param Response $response
     * @param int $statusCode
     * @return mixed
     */
    protected function assertAndDecodeJsonResponse($response, $statusCode = 200)
    {
        $this->assertEquals($statusCode, $response->getStatusCode(), $response->getContent());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json') || $response->getContent() === '', $response->headers);
        $this->assertJson($response->getContent(), $response->getContent());

        return json_decode($response->getContent(), true);
    }

} 
