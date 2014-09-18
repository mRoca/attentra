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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
                'id'           => method_exists($timePeriod, 'getId') ? $timePeriod->getId() : null,
                'title'        => $timePeriod->getIdentifier(),
                'start'        => $timePeriod->getStart()->format('c'),
                'end'          => $timePeriod->getEnd() ? $timePeriod->getEnd()->format('c') : $timePeriod->getStart()->add(new \DateInterval('PT1H'))->format('c'),
                'color'        => $timePeriod->getHasError() ? 'red' : null,

                //Not fullcalendar properties
                'identifier'   => $timePeriod->getIdentifier(),
                'endIsDefined' => is_object($timePeriod->getEnd())
            ];
        }

        return new JsonResponse($events);
    }

    /**
     * Update an event : update the first TimeInput, and create or update the second one
     * @Route("/eventupdate", name="attentra_calendar_eventupdate")
     * @Method("POST")
     */
    public function udpateEventAction(Request $request)
    {
        $post = $request->request->all();

        if (!isset($post['identifier'])) {
            throw new BadRequestHttpException("Event identifier not found.");
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var \DateTime $previousDate */
        $previousDate = null;
        foreach (array('start', 'end') as $cur) {

            if (!isset($post["{$cur}Id"], $post["{$cur}Date"], $post["{$cur}Time"])) {
                throw $this->createNotFoundException("Invalid $cur parameters.");
            }

            $newDate = null;

            if ($post["{$cur}Date"] && $post["{$cur}Time"]) {
                try {
                    $newDate = new \DateTime($post["{$cur}Date"] . ' ' . $post["{$cur}Time"]);
                } catch (\Exception $e) {
                    throw new BadRequestHttpException("Invalid $cur datetime format.");
                }
            }

            if ($newDate) {

                if ($previousDate) {
                    if ($this->timePeriodParser->getDateDay($previousDate) !== $this->timePeriodParser->getDateDay($newDate)) {
                        throw new BadRequestHttpException("Dates must concern the same day.");
                    }

                    if ($newDate->getTimestamp() <= $previousDate->getTimestamp()) {
                        throw new BadRequestHttpException("The $cur date must be greather than previous date.");
                    }
                }

                $previousDate = $newDate;
            }

            if ($post["{$cur}Id"]) {

                $timeInput = $em->getRepository('AttentraTimeBundle:TimeInput')->find($post["{$cur}Id"]);

                if (!$timeInput) {
                    throw $this->createNotFoundException('Unable to find the TimeInput entity.');
                }

                if ($timeInput->getIdentifier() !== $post['identifier']) {
                    throw $this->createNotFoundException('The TimeInput entity find has a different identifier.');
                }

                //No date, removing => TODO add an option to choose this action
                if ($newDate === null) {
                    $em->remove($timeInput);
                } else {
                    $timeInput->setDatetime($newDate);
                }

            } else if ($newDate) {
                $timeInput = new TimeInput();
                $timeInput->setDatetime($newDate);
                $timeInput->setIdentifier($post['identifier']);
                $em->persist($timeInput);
            }

        }

        $em->flush();

        return new JsonResponse(array('success' => true));
    }
}
