<?php

namespace Attentra\TimeBundle\Controller;

use Attentra\TimeBundle\Entity\TimeInput;
use Attentra\TimeBundle\Entity\TimePeriodInterface;
use Attentra\TimeBundle\Parser\TimePeriodsParserInterface;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Display Attentra time inputs entities on a calendar
 *
 * @Route("/calendar", service="attentra_time.calendar.controller")
 */
class CalendarController extends Controller
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
     * @Route("/", name="attentra_calendar")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $identifier = $this->get('request')->query->get('identifier');
        return array('identifier' => $identifier);
    }

    /**
     * Returns the time inputs with Fullcalendar json format
     *
     * @Route("/fullcalendar", name="attentra_calendar_fullcalendar")
     * @Method("GET")
     */
    public function fullcalendarAction()
    {
        $identifier = $this->get('request')->query->get('identifier');
        $start      = \DateTime::createFromFormat('Y-m-d', $this->get('request')->query->get('start'));
        $end        = \DateTime::createFromFormat('Y-m-d', $this->get('request')->query->get('end'));

        if (!$start || !$end) {
            $this->createNotFoundException('Invalid date parameters');
        }

        $start = $this->timePeriodParser->ajustStartDate($start);
        $end   = $this->timePeriodParser->ajustStartDate($end);


        /** @var EntityManager $em */
        $em         = $this->getDoctrine()->getManager();
        $timeinputs = $em->getRepository('AttentraTimeBundle:TimeInput')->qbFindByDates($identifier, $start, $end)->getQuery()->execute();

        $timePeriods = $this->timePeriodParser->timeInputsToEvents($timeinputs);

        $events = array();
        foreach ($timePeriods as $timePeriod) {
            /** @var TimePeriodInterface $timePeriod */
            $events[] = [
                'id'    => method_exists($timePeriod, 'getId') ? $timePeriod->getId() : null,
//                'identifier' => $timePeriod->getIdentifier(),
                'title' => $timePeriod->getIdentifier(),
                'start' => $timePeriod->getStart()->format('c'),
                'end'   => $timePeriod->getEnd() ? $timePeriod->getEnd()->format('c') : $timePeriod->getStart()->add(new \DateInterval('PT1H'))->format('c'),
                'color' => $timePeriod->getHasError() ? 'red' : null
            ];
        }

        return new JsonResponse($events);
    }
}
