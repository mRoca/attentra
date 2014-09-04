<?php

namespace Attentra\ApiBundle\Tests\Controller;

use Attentra\ApiBundle\Tests\Fixtures\Entity\LoadResourceData;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * TODO Create a Command to fill the database : tests are not the right way to do this
 */
class xFillDatabase extends WebTestCase
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

    public function testFilling()
    {
        $this->assertTrue(count(LoadResourceData::$resources) > 0, 'Database empty');
    }

}
