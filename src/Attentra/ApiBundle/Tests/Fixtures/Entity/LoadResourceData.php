<?php

namespace Attentra\ApiBundle\Tests\Fixtures\Entity;

use Attentra\ResourceBundle\Entity\Resource;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadResourceData extends AbstractFixture implements OrderedFixtureInterface
{
    /** @var \Attentra\ResourceBundle\Entity\Resource[] */
    static public $resources = [];

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        self::$resources = [];
        for ($i = 1; $i <= 20; $i++) {
            $resource = new Resource();
            $resource->setName('Resource ' . $i);
            $resource->setIdentifier('1234-' . $i);

            $resource->setGroup($this->getReference('resourcegroup-' . ($i % 4 + 1)));

            $manager->persist($resource);

            $this->addReference('resource-' . $i, $resource);
            self::$resources[] = $resource;
        }

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2;
    }
} 
