<?php

namespace Attentra\TimeBundle\Controller;

use Attentra\TimeBundle\Entity\TimeInput;
use Attentra\TimeBundle\Parser\TimePeriodsParserInterface;
use Attentra\TimeBundle\Parser\TimeSpentParser;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\Container;

/**
 * Display consummed time by period
 *
 * @Route("/planning", service="attentra_time.planning.controller")
 */
class PlanningController extends Controller
{
    /** @var TimePeriodsParserInterface */
    protected $timePeriodParser;

    public function __construct(Container $container, TimePeriodsParserInterface $timePeriodParser)
    {
        $this->setContainer($container);
        $this->timePeriodParser = $timePeriodParser;
    }

    /**
     * Display inputs on a calendar
     *
     * @Route("/", name="attentra_planning")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $identifier = $this->get('request')->query->get('identifier');
        $start      = \DateTime::createFromFormat('Y-m-d', $this->get('request')->query->get('start'));
        $end        = \DateTime::createFromFormat('Y-m-d', $this->get('request')->query->get('end'));

        list($start, $end) = $this->ajustStartEndDates($start, $end);

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var TimeInput[] $timeInputs */
        $timeInputs = $em->getRepository('AttentraTimeBundle:TimeInput')->qbFindByDates($identifier, $start, $end)->getQuery()->execute();

        $timeSpentManager = new TimeSpentParser($start, $end);
        $timeSpentManager->addTimePeriods($this->timePeriodParser->timeInputsToEvents($timeInputs));

        $identifiers = $em->getRepository('AttentraResourceBundle:Resource')->findBy(array(), array('name' => 'asc'));

        return array(
            'identifier'       => $identifier,
            'identifiers'      => $identifiers,
            'timeSpentManager' => $timeSpentManager,
            'start'            => $start,
            'end'              => $end,
        );
    }

    /**
     * Display time spent by resources by week & month
     *
     * @Route("/summary", name="attentra_planning_summary")
     * @Method("GET")
     * @Template()
     */
    public function summaryAction()
    {
        $start = \DateTime::createFromFormat('Y-m-d', $this->get('request')->query->get('start'));
        $end   = \DateTime::createFromFormat('Y-m-d', $this->get('request')->query->get('end'));

        list($start, $end) = $this->ajustStartEndDates($start, $end);

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var TimeInput[] $timeInputs */
        $timeInputs = $em->getRepository('AttentraTimeBundle:TimeInput')->qbFindByDates(null, $start, $end)->getQuery()->execute();

        $timeInputsByIdentifier = [];
        foreach ($timeInputs as $timeInput) {
            $timeInputsByIdentifier[$timeInput->getIdentifier()][] = $timeInput;
        }

        unset($timeInputs);

        /** @var TimeSpentParser[] $timeSpentManagers */
        $timeSpentManagers = [];
        foreach ($timeInputsByIdentifier as $identifier => $timeInputs) {
            $timeSpentManagers[$identifier] = new TimeSpentParser($start, $end);
            $timeSpentManagers[$identifier]->addTimePeriods($this->timePeriodParser->timeInputsToEvents($timeInputs));
        }

        return array(
            'timeSpentManagers' => $timeSpentManagers,
            'start'             => $start,
            'end'               => $end,
        );
    }

    /**
     * @param \Datetime $start
     * @param \Datetime $end
     * @return \Datetime[]
     */
    protected function ajustStartEndDates($start, $end)
    {
        $end = $this->timePeriodParser->ajustStartDate($end !== false ? $end : new \DateTime('tomorrow'));

        //By default, display one month
        if ($start === false) {
            $start = clone $end;
            $start->sub(new \DateInterval('P1M1D'));
        } elseif ($end < $start) {
            $end = clone $start;
            $end->add(new \DateInterval('P1M1D'));
        }

        $start = $this->timePeriodParser->ajustStartDate($start);

        return [$start, $end];
    }
}
