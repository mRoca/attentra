<?php

namespace Attentra\ApiBundle\Tests\Controller;

use Attentra\ApiBundle\Tests\Fixtures\Entity\LoadResourceData;
use Attentra\ApiBundle\Tests\Fixtures\Entity\LoadResourceGroupData;
use Attentra\CoreBundle\Tests\Controller\RestWebTestCase;

class ResourceController extends RestWebTestCase
{
    public function setUp()
    {
        parent::setUp();

        $classes = array(
            'Attentra\ApiBundle\Tests\Fixtures\Entity\LoadResourceGroupData',
            'Attentra\ApiBundle\Tests\Fixtures\Entity\LoadResourceData',
        );
        $this->loadFixtures($classes);
    }

    public function testJsonGetResourcesAction()
    {
        $resources = LoadResourceData::$resources;

        $route    = $this->getUrl('attentra_api_get_resources');
        $response = $this->sendJsonRequest('GET', $route);
        $decoded  = $this->assertAndDecodeJsonResponse($response, 200);

        $this->assertEquals(min(5, count($resources)), count($decoded));
        $this->assertEqualsAndSet($resources[0]->getId(), $decoded[0]['id']);
    }

    public function testJsonGetResourceAction()
    {
        $resource = LoadResourceData::$resources[0];

        $route    = $this->getUrl('attentra_api_get_resource', ['id' => $resource->getId()]);
        $response = $this->sendJsonRequest('GET', $route);
        $decoded  = $this->assertAndDecodeJsonResponse($response, 200);

        $this->assertEqualsAndSet($resource->getId(), $decoded['id']);
    }

    public function testJsonNewResourceAction()
    {
        $route    = $this->getUrl('attentra_api_new_resource');
        $response = $this->sendJsonRequest('GET', $route);
        $decoded  = $this->assertAndDecodeJsonResponse($response, 200);

        $this->assertArrayHasKey('children', $decoded);
        $this->assertArrayHasKey('name', $decoded['children']);
    }

    public function testJsonPostResourceAction()
    {
        $route = $this->getUrl('attentra_api_post_resource');

        $response = $this->sendJsonRequest('POST', $route, '');
        $this->assertAndDecodeJsonResponse($response, 400);

        $group     = LoadResourceGroupData::$resourcegroups[0];
        $newObject = [
            'name'       => 'New object',
            'identifier' => '12345',
            'group'      => $group->getId()
        ];

        $response = $this->sendJsonRequest('POST', $route, json_encode($newObject));
        $this->assertAndDecodeJsonResponse($response, 201);
    }

    public function testJsonPutResourceAction()
    {
        $route = $this->getUrl('attentra_api_put_resource', ['id' => -1]);

        $response = $this->sendJsonRequest('PUT', $route, json_encode(['name' => 'New put object']));
        $this->assertAndDecodeJsonResponse($response, 201);

        $resource = LoadResourceData::$resources[0];
        $route    = $this->getUrl('attentra_api_put_resource', ['id' => $resource->getId()]);

        $response = $this->sendJsonRequest('PUT', $route, json_encode(['identifier' => '687468']));
        $this->assertAndDecodeJsonResponse($response, 400);

        $response = $this->sendJsonRequest('PUT', $route, json_encode(['name' => 'Updated put object']));
        $this->assertAndDecodeJsonResponse($response, 204);
    }

    public function testJsonPatchResourceAction()
    {
        $resource = LoadResourceData::$resources[0];
        $route    = $this->getUrl('attentra_api_patch_resource', ['id' => $resource->getId()]);

        $response = $this->sendJsonRequest('PATCH', $route, json_encode(['identifier' => '687468']));
        $this->assertAndDecodeJsonResponse($response, 204);
    }

    public function testJsonDeleteResourceAction()
    {
        $resource = LoadResourceData::$resources[0];
        $route    = $this->getUrl('attentra_api_delete_resource', ['id' => $resource->getId()]);

        $response = $this->sendJsonRequest('DELETE', $route);
        $this->assertAndDecodeJsonResponse($response, 204);
    }
}
