<?php

namespace Attentra\ApiBundle\Tests\Controller;

use Attentra\ApiBundle\Tests\Fixtures\Entity\LoadResourceGroupData;
use Attentra\CoreBundle\Tests\Controller\RestWebTestCase;

class ResourceGroupController extends RestWebTestCase
{
    public function setUp()
    {
        parent::setUp();

        $classes = array(
            'Attentra\ApiBundle\Tests\Fixtures\Entity\LoadResourceGroupData',
        );
        $this->loadFixtures($classes);
    }

    public function testJsonGetResourceGroupsAction()
    {
        $groups = LoadResourceGroupData::$resourcegroups;

        $route    = $this->getUrl('attentra_api_get_resourcegroups');
        $response = $this->sendJsonRequest('GET', $route);
        $decoded  = $this->assertAndDecodeJsonResponse($response, 200);

        $this->assertEquals(min(5, count($groups)), count($decoded));
        $this->assertEqualsAndSet($groups[0]->getId(), $decoded[0]['id']);
    }

    public function testJsonGetResourceGroupAction()
    {
        $group = LoadResourceGroupData::$resourcegroups[0];

        $route    = $this->getUrl('attentra_api_get_resourcegroup', ['id' => $group->getId()]);
        $response = $this->sendJsonRequest('GET', $route);
        $decoded  = $this->assertAndDecodeJsonResponse($response, 200);

        $this->assertEqualsAndSet($group->getId(), $decoded['id']);
    }

    public function testJsonNewResourceGroupAction()
    {
        $route    = $this->getUrl('attentra_api_new_resourcegroup');
        $response = $this->sendJsonRequest('GET', $route);
        $decoded  = $this->assertAndDecodeJsonResponse($response, 200);

        $this->assertArrayHasKey('children', $decoded);
        $this->assertArrayHasKey('name', $decoded['children']);
    }

    public function testJsonPostResourceGroupAction()
    {
        $route = $this->getUrl('attentra_api_post_resourcegroup');

        $response = $this->sendJsonRequest('POST', $route, '');
        $this->assertAndDecodeJsonResponse($response, 400);

        $response = $this->sendJsonRequest('POST', $route, json_encode(['name' => 'New group']));
        $this->assertAndDecodeJsonResponse($response, 201);
    }

    public function testJsonPutResourceGroupAction()
    {
        $route = $this->getUrl('attentra_api_put_resourcegroup', ['id' => -1]);

        $response = $this->sendJsonRequest('PUT', $route, json_encode(['name' => 'New put group']));
        $this->assertAndDecodeJsonResponse($response, 201);

        $group = LoadResourceGroupData::$resourcegroups[0];
        $route = $this->getUrl('attentra_api_put_resourcegroup', ['id' => $group->getId()]);

        $response = $this->sendJsonRequest('PUT', $route, json_encode(['description' => 'My new desc']));
        $this->assertAndDecodeJsonResponse($response, 400);

        $response = $this->sendJsonRequest('PUT', $route, json_encode(['name' => 'Updated put group']));
        $this->assertAndDecodeJsonResponse($response, 204);
    }

    public function testJsonPatchResourceGroupAction()
    {
        $group = LoadResourceGroupData::$resourcegroups[0];
        $route = $this->getUrl('attentra_api_patch_resourcegroup', ['id' => $group->getId()]);

        $response = $this->sendJsonRequest('PATCH', $route, json_encode(['description' => 'My new desc']));
        $this->assertAndDecodeJsonResponse($response, 204);
    }

    public function testJsonDeleteResourceGroupAction()
    {
        $group = LoadResourceGroupData::$resourcegroups[0];
        $route = $this->getUrl('attentra_api_delete_resourcegroup', ['id' => $group->getId()]);

        $response = $this->sendJsonRequest('DELETE', $route);
        $this->assertAndDecodeJsonResponse($response, 204);
    }
}
