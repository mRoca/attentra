<?php

namespace Attentra\TimeBundle\Tests\Parser;

use Attentra\TimeBundle\Entity\TimePeriod;
use Attentra\TimeBundle\Parser\TimeSpentParser;

class TimeSpentParserTest extends \PHPUnit_Framework_TestCase
{

    public function testGetPeriodStartDate()
    {
        $parser = new TimeSpentParser(new \DateTime('2014-08-23 03:00:00'), new \DateTime('2014-09-23 03:00:00'));

        $this->assertEquals(new \DateTime('2014-08-28 00:00:00'), $parser->getPeriodStartDate('day', new \DateTime('2014-08-28 03:00:00')));
        $this->assertEquals(new \DateTime('2014-08-25 00:00:00'), $parser->getPeriodStartDate('week', new \DateTime('2014-08-28 03:00:00')));
        $this->assertEquals(new \DateTime('2014-08-01 00:00:00'), $parser->getPeriodStartDate('month', new \DateTime('2014-08-28 03:00:00')));
        $this->assertEquals(new \DateTime('2014-01-01 00:00:00'), $parser->getPeriodStartDate('year', new \DateTime('2014-08-28 03:00:00')));
    }

    public function testGetPeriodEndDate()
    {
        $parser = new TimeSpentParser(new \DateTime('2014-08-23 03:00:00'), new \DateTime('2014-09-23 03:00:00'));

        $this->assertEquals(new \DateTime('2014-08-28 00:00:00'), $parser->getPeriodEndDate('day', new \DateTime('2014-08-28 03:00:00')));
        $this->assertEquals(new \DateTime('2014-08-31 00:00:00'), $parser->getPeriodEndDate('week', new \DateTime('2014-08-28 03:00:00')));
        $this->assertEquals(new \DateTime('2014-08-31 00:00:00'), $parser->getPeriodEndDate('month', new \DateTime('2014-08-28 03:00:00')));
        $this->assertEquals(new \DateTime('2014-12-31 00:00:00'), $parser->getPeriodEndDate('year', new \DateTime('2014-08-28 03:00:00')));
    }

    public function testGetDatePeriod()
    {
        $parser = new TimeSpentParser(new \DateTime('2014-08-23 03:00:00'), new \DateTime('2014-09-23 03:00:00'));

        $this->assertInstanceOf('DatePeriod', $parser->getDatePeriod());

        $this->assertEqualsDatePeriod(new \DatePeriod(new \DateTime('2014-08-23 00:00:00'), new \DateInterval('P1D'), new \DateTime('2014-09-24 00:00:00')), $parser->getDatePeriod('day'));
        $this->assertEqualsDatePeriod(new \DatePeriod(new \DateTime('2014-08-18 00:00:00'), new \DateInterval('P1W'), new \DateTime('2014-09-28 00:00:00')), $parser->getDatePeriod('week'));
        $this->assertEqualsDatePeriod(new \DatePeriod(new \DateTime('2014-08-01 00:00:00'), new \DateInterval('P1M'), new \DateTime('2014-09-30 00:00:00')), $parser->getDatePeriod('month'));
        $this->assertEqualsDatePeriod(new \DatePeriod(new \DateTime('2014-01-01 00:00:00'), new \DateInterval('P1Y'), new \DateTime('2014-12-31 00:00:00')), $parser->getDatePeriod('year'));
    }

    public function testHasFullPeriod()
    {
        $parser = new TimeSpentParser(new \DateTime('2014-08-26 03:00:00'), new \DateTime('2014-09-23 03:00:00'));
        $this->assertEquals(true, $parser->hasFullPeriod('day'));
        $this->assertEquals(true, $parser->hasFullPeriod('week'));
        $this->assertEquals(false, $parser->hasFullPeriod('month'));
        $this->assertEquals(false, $parser->hasFullPeriod('year'));
    }

    public function testIsPeriodFull()
    {
        $parser = new TimeSpentParser(new \DateTime('2014-08-26 03:00:00'), new \DateTime('2014-09-23 03:00:00'));
        $this->assertEquals(true, $parser->isPeriodFull('day', new \DateTime('2014-09-23')));
        $this->assertEquals(false, $parser->isPeriodFull('day', new \DateTime('2014-09-24')));
        $this->assertEquals(true, $parser->isPeriodFull('week', new \DateTime('2014-09-20')));
        $this->assertEquals(false, $parser->isPeriodFull('week', new \DateTime('2014-09-23')));
        $this->assertEquals(false, $parser->isPeriodFull('month', new \DateTime('2014-09-10')));
        $this->assertEquals(false, $parser->isPeriodFull('month', new \DateTime('2014-08-25')));
        $this->assertEquals(false, $parser->isPeriodFull('year', new \DateTime('2014-08-25')));
    }

    public function testIsLastPeriod()
    {
        $parser = new TimeSpentParser(new \DateTime('2014-08-26 03:00:00'), new \DateTime('2014-09-23 03:00:00'));
        $this->assertEquals(true, $parser->isLastPeriod('day', new \DateTime('2014-09-23')));
        $this->assertEquals(false, $parser->isLastPeriod('day', new \DateTime('2014-09-22')));
        $this->assertEquals(true, $parser->isLastPeriod('week', new \DateTime('2014-09-23')));
        $this->assertEquals(true, $parser->isLastPeriod('week', new \DateTime('2014-09-22')));
        $this->assertEquals(true, $parser->isLastPeriod('month', new \DateTime('2014-09-22')));
        $this->assertEquals(false, $parser->isLastPeriod('month', new \DateTime('2014-08-30')));
        $this->assertEquals(true, $parser->isLastPeriod('year', new \DateTime('2014-08-30')));
    }

    public function testGetTimePeriodsByPeriod()
    {
        $parser = new TimeSpentParser(new \DateTime('2014-08-01 03:00:00'), new \DateTime('2014-09-25 03:00:00'));
        $parser->addTimePeriods($this->generateTimePeriods());

        $this->assertEquals(2, count($parser->getTimePeriodsByPeriod('day', new \DateTime('2014-08-23'))));
        $this->assertEquals(0, count($parser->getTimePeriodsByPeriod('week', new \DateTime('2014-09-10'))));
        $this->assertEquals(3, count($parser->getTimePeriodsByPeriod('month', new \DateTime('2014-09-23'))));
        $this->assertEquals(5, count($parser->getTimePeriodsByPeriod('year', new \DateTime('2014-08-01'))));
    }

    public function testGetSpentTimeIntervalByPeriod()
    {
        $parser = new TimeSpentParser(new \DateTime('2014-08-26 03:00:00'), new \DateTime('2014-09-23 03:00:00'));
        $parser->addTimePeriods($this->generateTimePeriods());

        $this->assertEquals(1 * 3600 + 35 * 60 + 30, $parser->getSpentTimeIntervalByPeriod('day', new \DateTime('2014-08-23'))->toSeconds());
        $this->assertEquals(0, $parser->getSpentTimeIntervalByPeriod('week', new \DateTime('2014-09-10'))->toSeconds());
        $this->assertEquals(1 * 3600 + 35 * 60 + 30, $parser->getSpentTimeIntervalByPeriod('month', new \DateTime('2014-09-23'))->toSeconds());
        $this->assertEquals(2 * 3600 + 70 * 60 + 60, $parser->getSpentTimeIntervalByPeriod('year', new \DateTime('2014-08-01'))->toSeconds());
    }

    public function testGettersSetters()
    {
        $parser = new TimeSpentParser(new \DateTime('2014-08-26 03:00:00'), new \DateTime('2014-09-23 03:00:00'));
        $this->assertEquals(0, count($parser->getTimePeriods()));

        $parser->addTimePeriods($this->generateTimePeriods());
        $this->assertEquals(5, count($parser->getTimePeriods()));

        $parser->addTimePeriod(new TimePeriod());
        $this->assertEquals(6, count($parser->getTimePeriods()));

        $this->assertEquals(new \DateTime('2014-08-26 03:00:00'), $parser->getStart());
        $parser->setStart(new \DateTime('2014-08-26 04:00:00'));
        $this->assertEquals(new \DateTime('2014-08-26 04:00:00'), $parser->getStart());

        $this->assertEquals(new \DateTime('2014-09-23 03:00:00'), $parser->getEnd());
        $parser->setEnd(new \DateTime('2014-09-26 04:00:00'));
        $this->assertEquals(new \DateTime('2014-09-26 04:00:00'), $parser->getEnd());
    }

    // ----------------------------------------

    protected function generateTimePeriods()
    {
        $t1 = new TimePeriod();
        $t1->setStart(new \DateTime('2014-08-23 10:00:00'));
        $t1->setEnd(new \DateTime('2014-08-23 11:30:30'));
        $t1->setConcernedDay(new \DateTime('2014-08-23 00:00:00'));

        $t2 = new TimePeriod();
        $t2->setStart(new \DateTime('2014-08-23 11:50:00'));
        $t2->setEnd(new \DateTime('2014-08-23 11:55:00'));
        $t2->setConcernedDay(new \DateTime('2014-08-23 00:00:00'));

        $t3 = new TimePeriod();
        $t3->setStart(new \DateTime('2014-09-23 10:00:00'));
        $t3->setEnd(new \DateTime('2014-09-23 11:30:30'));
        $t3->setConcernedDay(new \DateTime('2014-09-23 00:00:00'));

        $t4 = new TimePeriod();
        $t4->setStart(new \DateTime('2014-09-23 11:50:00'));
        $t4->setEnd(new \DateTime('2014-09-23 11:55:00'));
        $t4->setConcernedDay(new \DateTime('2014-09-23 00:00:00'));

        $t5 = new TimePeriod();
        $t5->setStart(new \DateTime('2014-09-23 12:50:00'));
        $t5->setConcernedDay(new \DateTime('2014-09-23 00:00:00'));

        $timePeriods = [];
        for ($i = 1; $i <= 5; $i++) {
            $timePeriods[] = ${"t$i"};
        }

        return $timePeriods;
    }

    protected function assertEqualsDatePeriod(\DatePeriod $expected, \DatePeriod $actual)
    {
        $expectedArray = [];
        foreach ($expected as $dayDate) {
            $expectedArray[] = $dayDate;
        }

        foreach ($actual as $i => $dayDate) {
            $this->assertArrayHasKey($i, $expectedArray);
            $this->assertEquals($expectedArray[$i], $dayDate);
        }
    }

}
