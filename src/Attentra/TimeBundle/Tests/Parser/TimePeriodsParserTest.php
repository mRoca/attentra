<?php

namespace Attentra\TimeBundle\Tests\Parser;

use Attentra\TimeBundle\Entity\TimeInput;
use Attentra\TimeBundle\Parser\TimePeriodsParser;

class TimePeriodsParserTest extends \PHPUnit_Framework_TestCase
{

    public function testAjustStartDate()
    {
        $parser = new TimePeriodsParser('Attentra\TimeBundle\Entity\TimePeriod', '03:01:05');

        $expectedDate = new \DateTime('2014-09-09 03:01:05');
        $parsedDdate  = $parser->ajustStartDate(new \DateTime('2014-09-09 01:01:01'));

        $this->assertEquals($expectedDate, $parsedDdate);
    }

    public function testAjustEndDate()
    {
        $parser = new TimePeriodsParser('Attentra\TimeBundle\Entity\TimePeriod', '03:01:05');

        $expectedDate = new \DateTime('2014-09-09 03:01:05');
        $parsedDdate  = $parser->ajustStartDate(new \DateTime('2014-09-09 01:01:01'));

        $this->assertEquals($expectedDate, $parsedDdate);
    }

    public function testTimeInputsToEvents()
    {
        $parser = new TimePeriodsParser('Attentra\TimeBundle\Entity\TimePeriod', '03:01:05');

        //Before 03:01:05
        $t1 = new TimeInput();
        $t1->setIdentifier('1234-1');
        $t1->setDatetime(new \DateTime('2014-09-08 22:01:01'));

        $t2 = new TimeInput();
        $t2->setIdentifier('1234-1');
        $t2->setDatetime(new \DateTime('2014-09-09 01:02:01'));

        //Unfinished event
        $t3 = new TimeInput();
        $t3->setIdentifier('1234-1');
        $t3->setDatetime(new \DateTime('2014-09-09 02:02:01'));

        //After 03:01:05
        $t4 = new TimeInput();
        $t4->setIdentifier('1234-1');
        $t4->setDatetime(new \DateTime('2014-09-09 03:02:01'));

        $t5 = new TimeInput();
        $t5->setIdentifier('1234-1');
        $t5->setDatetime(new \DateTime('2014-09-09 10:10:10'));

        //Other identifier
        $t6 = new TimeInput();
        $t6->setIdentifier('1234-2');
        $t6->setDatetime(new \DateTime('2014-09-09 10:10:10'));

        $t7 = new TimeInput();
        $t7->setIdentifier('1234-2');
        $t7->setDatetime(new \DateTime('2014-09-09 12:10:10'));

        $timeInputs = [];
        for ($i = 1; $i < 7; $i++) {
            $timeInputs[] = ${"t$i"};
        }

        $events = $parser->timeInputsToEvents($timeInputs);

        $this->assertEquals(4, count($events));

        //Unfinished event
        $this->assertEquals(true, $events[1]->getHasError());

        //First 2014-09-09 event - after 03:01:05
        $this->assertEquals($t5->getDatetime(), $events[2]->getEnd());
    }

    public function testGroupTimeInputs()
    {
        $parser = new TimePeriodsParser('Attentra\TimeBundle\Entity\TimePeriod', '03:01:05');

        //After 03:01:05
        $t1 = new TimeInput();
        $t1->setIdentifier('1234-1');
        $t1->setDatetime(new \DateTime('2014-09-09 03:02:01'));

        //Before 03:01:05
        $t2 = new TimeInput();
        $t2->setIdentifier('1234-1');
        $t2->setDatetime(new \DateTime('2014-09-09 02:01:01'));

        //Other identifier
        $t3 = new TimeInput();
        $t3->setIdentifier('1234-2');
        $t3->setDatetime(new \DateTime('2014-09-09 10:10:10'));

        $groupedInputs = $parser->groupTimeInputs([$t1, $t2, $t3]);

        //Group by identifier
        $this->assertEquals(2, count($groupedInputs));
        $this->assertArrayHasKey('1234-1', $groupedInputs);

        //Then group by day, using 03:01:05 as separator
        $this->assertEquals(2, count($groupedInputs['1234-1']));
        $this->assertArrayHasKey('2014-09-08', $groupedInputs['1234-1']);
        $this->assertEquals(1, count($groupedInputs['1234-1']['2014-09-08']));
    }

}
