<?php

namespace Attentra\ApiBundle\Tests\Fixtures\Entity;

use Attentra\ResourceBundle\Entity\ResourceGroup;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadResourceGroupData extends AbstractFixture implements OrderedFixtureInterface
{
    /** @var \Attentra\ResourceBundle\Entity\ResourceGroup[] */
    static public $resourcegroups = [];

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        self::$resourcegroups = [];
        for ($i = 1; $i <= 4; $i++) {
            $group = new ResourceGroup();
            $group->setName('Group ' . $i);

            $manager->persist($group);

            $this->addReference('resourcegroup-' . $i, $group);
            self::$resourcegroups[] = $group;
        }

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }
} 
