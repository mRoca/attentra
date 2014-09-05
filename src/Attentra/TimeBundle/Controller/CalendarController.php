<?php

namespace Attentra\TimeBundle\Controller;

use Attentra\TimeBundle\Entity\TimeInput;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Display Attentra time inputs entities on a calendar
 *
 * @Route("/calendar")
 */
class CalendarController extends Controller
{
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
        $start      = $this->get('request')->query->get('start');
        $end        = $this->get('request')->query->get('end');

        if (!$start || !$end) {
            $this->createNotFoundException('Invalid date parameters');
        }

        /** @var EntityManager $em */
        $em         = $this->getDoctrine()->getManager();
        $timeinputs = $em->getRepository('AttentraTimeBundle:TimeInput')->qbFindByDates($identifier, new \DateTime($start), new \DateTime($end))->getQuery()->execute();;

        $events = array();
        foreach ($timeinputs as $timeinput) {
            /** @var TimeInput $timeinput */
            $events[] = [
                'id'    => $timeinput->getId(),
                'title' => $timeinput->getIdentifier(),
                'start' => $timeinput->getDatetime()->format('c'),
                'end'   => $timeinput->getDatetime()->format('c'),
            ];
        }

        return new JsonResponse($events);
    }
}
