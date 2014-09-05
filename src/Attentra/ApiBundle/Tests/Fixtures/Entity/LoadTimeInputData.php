<?php

namespace Attentra\ApiBundle\Tests\Fixtures\Entity;

use Attentra\TimeBundle\Entity\TimeInput;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadTimeInputData extends AbstractFixture implements OrderedFixtureInterface
{
    /** @var \Attentra\TimeBundle\Entity\TimeInput[] */
    static public $timeinputs = [];

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        self::$timeinputs = [];
        for ($i = 1; $i <= 100; $i++) {
            $timeinput = new TimeInput();
            $timeinput->setDatetime(new \DateTime(rand(0, 700000) . 'seconds ago'));
            $timeinput->setIdentifier('1234-' . ($i % 20));

            $manager->persist($timeinput);

            self::$timeinputs[] = $timeinput;
        }

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 3;
    }
}
