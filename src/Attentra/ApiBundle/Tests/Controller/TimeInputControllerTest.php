<?php

namespace Attentra\ApiBundle\Tests\Controller;

use Attentra\ApiBundle\Tests\Fixtures\Entity\LoadTimeInputData;
use Attentra\CoreBundle\Tests\Controller\RestWebTestCase;

class TimeInputController extends RestWebTestCase
{
    public function setUp()
    {
        parent::setUp();

        $classes = array(
            'Attentra\ApiBundle\Tests\Fixtures\Entity\LoadTimeInputData',
        );
        $this->loadFixtures($classes);
    }

    public function testJsonGetTimeInputsAction()
    {
        $timeinputs = LoadTimeInputData::$timeinputs;

        $route    = $this->getUrl('attentra_api_get_timeinputs');
        $response = $this->sendJsonRequest('GET', $route);
        $decoded  = $this->assertAndDecodeJsonResponse($response, 200);

        $this->assertEquals(min(5, count($timeinputs)), count($decoded));
        $this->assertEqualsAndSet($timeinputs[0]->getId(), $decoded[0]['id']);
    }

    public function testJsonGetTimeInputAction()
    {
        $timeinput = LoadTimeInputData::$timeinputs[0];

        $route    = $this->getUrl('attentra_api_get_timeinput', ['id' => $timeinput->getId()]);
        $response = $this->sendJsonRequest('GET', $route);
        $decoded  = $this->assertAndDecodeJsonResponse($response, 200);

        $this->assertEqualsAndSet($timeinput->getId(), $decoded['id']);
    }

    public function testJsonNewTimeInputAction()
    {
        $route    = $this->getUrl('attentra_api_new_timeinput');
        $response = $this->sendJsonRequest('GET', $route);
        $decoded  = $this->assertAndDecodeJsonResponse($response, 200);

        $this->assertArrayHasKey('children', $decoded);
        $this->assertArrayHasKey('datetime', $decoded['children']);
    }

    public function testJsonPostTimeInputAction()
    {
        $route = $this->getUrl('attentra_api_post_timeinput');

        $response = $this->sendJsonRequest('POST', $route, '');
        $this->assertAndDecodeJsonResponse($response, 400);

        $response = $this->sendJsonRequest('POST', $route, json_encode(['datetime' => gmdate(DATE_ATOM), 'identifier' => '1234-1']));
        $this->assertAndDecodeJsonResponse($response, 201);
    }

    public function testJsonPutTimeInputAction()
    {
        $route = $this->getUrl('attentra_api_put_timeinput', ['id' => -1]);

        $response = $this->sendJsonRequest('PUT', $route, json_encode(['datetime' => gmdate(DATE_ATOM), 'identifier' => '1234-2']));
        $this->assertAndDecodeJsonResponse($response, 201);

        $timeinput = LoadTimeInputData::$timeinputs[0];
        $route     = $this->getUrl('attentra_api_put_timeinput', ['id' => $timeinput->getId()]);

        $response = $this->sendJsonRequest('PUT', $route, json_encode(['description' => 'My new desc']));
        $this->assertAndDecodeJsonResponse($response, 400);

        $response = $this->sendJsonRequest('PUT', $route, json_encode(['datetime' => gmdate(DATE_ATOM), 'identifier' => '1234-5']));
        $this->assertAndDecodeJsonResponse($response, 204);
    }

    public function testJsonPatchTimeInputAction()
    {
        $timeinput = LoadTimeInputData::$timeinputs[0];
        $route     = $this->getUrl('attentra_api_patch_timeinput', ['id' => $timeinput->getId()]);

        $response = $this->sendJsonRequest('PATCH', $route, json_encode(['description' => 'My new desc']));
        $this->assertAndDecodeJsonResponse($response, 204);
    }

    public function testJsonDeleteTimeInputAction()
    {
        $timeinput = LoadTimeInputData::$timeinputs[0];
        $route     = $this->getUrl('attentra_api_delete_timeinput', ['id' => $timeinput->getId()]);

        $response = $this->sendJsonRequest('DELETE', $route);
        $this->assertAndDecodeJsonResponse($response, 204);
    }
}
