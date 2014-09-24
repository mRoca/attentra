<?php

namespace Attentra\TimeBundle\Tests\Parser;

use Attentra\CoreBundle\Tests\TestCase;
use Attentra\TimeBundle\Entity\TimeInput;
use Attentra\TimeBundle\Entity\TimeInterval;
use Attentra\TimeBundle\Parser\TimePeriodsParser;

class TimePeriodsParserTest extends TestCase
{
    public function testConstruct()
    {
        $this->assertException(function () {
            new TimePeriodsParser('Attentra\TimeBundle\Entity\TimePeriod', 'imnotatimeformat');
        }, 'ErrorException');

        $this->assertException(function () {
            new TimePeriodsParser('Attentra\TimeBundle\Entity\TimePeriod', 'imnotatimeformat');
        }, 'ErrorException');
    }

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
        $parsedDdate  = $parser->ajustEndDate(new \DateTime('2014-09-09 01:01:01'));

        $this->assertEquals($expectedDate, $parsedDdate);
    }

    public function testTimeInputsToEvents()
    {
        $parser = new TimePeriodsParser('Attentra\TimeBundle\Entity\TimePeriod', '03:01:05');

        $timeInputs = $this->generateTimeInputs();

        $events = $parser->timeInputsToEvents($timeInputs);

        $this->assertEquals(4, count($events));

        //Unfinished event
        $this->assertEquals(true, $events[1]->getHasError());

        //First 2014-09-09 event (after 03:01:05)
        $this->assertEquals(new \DateTime('2014-09-09 10:10:10'), $events[2]->getEnd());
    }

    public function testTimeInputsToSpentTimeByDay()
    {
        $parser = new TimePeriodsParser('Attentra\TimeBundle\Entity\TimePeriod', '03:01:05');

        $timeInputs = $this->generateTimeInputs();

        $timeIntervals = $parser->timeInputsToSpentTimeByDay($timeInputs);

        $this->assertEquals(2, count($timeIntervals));

        //First day : 1 full event
        $this->assertEquals(new TimeInterval('PT3H1M'), $timeIntervals['2014-09-08']);

        //Second day : 2 full events for 2 different identifiers
        $this->assertEquals(new TimeInterval('PT9H8M9S'), $timeIntervals['2014-09-09']);
    }

    public function testGroupTimeInputs()
    {
        $parser = new TimePeriodsParser('Attentra\TimeBundle\Entity\TimePeriod', '03:01:05');

        $timeInputs    = $this->generateTimeInputs();
        $groupedInputs = $parser->groupTimeInputs($timeInputs);

        //Group by identifier
        $this->assertEquals(2, count($groupedInputs));
        $this->assertArrayHasKey('1234-1', $groupedInputs);
        $this->assertArrayHasKey('1234-2', $groupedInputs);

        //Then group by day, using 03:01:05 as separator
        $this->assertEquals(2, count($groupedInputs['1234-1']));
        $this->assertArrayHasKey('2014-09-08', $groupedInputs['1234-1']);
        $this->assertEquals(3, count($groupedInputs['1234-1']['2014-09-08']));
        $this->assertEquals(2, count($groupedInputs['1234-1']['2014-09-09']));
        $this->assertEquals(2, count($groupedInputs['1234-2']['2014-09-09']));
    }

    public function testGetDateDay()
    {
        $parser = new TimePeriodsParser('Attentra\TimeBundle\Entity\TimePeriod', '03:01:05');

        //Return string
        $this->assertEquals('2014-09-08', $parser->getDateDay(new \DateTime('2014-09-08 17:00:00'), true));

        //Return object
        $this->assertEquals(new \DateTime('2014-09-08 00:00:00'), $parser->getDateDay(new \DateTime('2014-09-09 02:00:00'), false));
        $this->assertEquals(new \DateTime('2014-09-09 00:00:00'), $parser->getDateDay(new \DateTime('2014-09-09 04:00:00'), false));
    }

    public function testGetWorkDayStartHour()
    {
        $parser = new TimePeriodsParser('Attentra\TimeBundle\Entity\TimePeriod', '03:01:05');

        //Return string
        $this->assertEquals('03:01:05', $parser->getWorkDayStartHour());

        //Return array
        $expected     = ['hour' => '03', 'minute' => '01', 'second' => '05'];
        $workDayArray = $parser->getWorkDayStartHour(true);

        foreach ($expected as $key => $value) {
            $this->assertArrayHasKey($key, $workDayArray);
            $this->assertEquals($value, $workDayArray[$key]);
        }
    }

    public function testSetWorkDayStartHour()
    {
        $parser = new TimePeriodsParser('Attentra\TimeBundle\Entity\TimePeriod', '03:01:05');

        $parser->setWorkDayStartHour('01:00:00');

        //Return string
        $this->assertEquals('01:00:00', $parser->getWorkDayStartHour());
    }

    // ----------------------------------------

    /**
     * @return TimeInput[]
     */
    protected function generateTimeInputs()
    {
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
        for ($i = 1; $i <= 7; $i++) {
            $timeInputs[] = ${"t$i"};
        }

        return $timeInputs;
    }
}
