<?php
// src/Controller/DefaultController.php
namespace App\Controller;

use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\Service\CommonGroundService;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use App\Security\User\CommongroundUser;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class PdcController
 * @package App\Controller
 * @Route("/arc")
 */
class ArcController extends AbstractController
{

	/**
	 * @Route("/")
	 * @Template
	 */
	public function indexAction(TranslatorInterface $translator)
	{
		$variables = [];
		$variables['title'] = $translator->trans('Calendars and schedule');
		$variables['subtitle'] = $translator->trans('These dashboards allows the administration of calendar entities, with schedules, resources, events, todos, journals, freebusies and alarms');

		return $variables;
	}

    /**
     * @Route("/resources")
     * @Template
     */
	public function resourcesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
    	$variables = [];
    	$variables['title'] = $translator->trans('resources');
    	$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('resources');
    	$variables['resources'] = $commonGroundService->getResourceList(['component'=>'arc', 'type'=>'resources'])["hydra:member"];

        return $variables;
    }

    /**
     * @Route("/resources/{id}")
     * @Template
     */
    public function resourceAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

        $variables = [];

        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource(['component'=>'arc','type'=>'resources','id'=>$id]);
            return $this->redirect($this->generateUrl('app_arc_resources'));
        }
        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'arc', 'type'=>'resources','id'=>$id]);
        }

        $variables['title'] = $translator->trans('resource');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('resource');

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource,['component'=>'arc', 'type'=>'resources']);
        }

        return $variables;
    }

//    /**
//     * @Route("/events")
//     * @Template
//     */
//    public function eventsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
//    {
//        $variables = [];
//        $variables['title'] = $translator->trans('events');
//        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('events');
//        $variables['resources'] = $commonGroundService->getResourceList('https://ac.huwelijksplanner.online/events')["hydra:member"];
//
//        return $variables;
//    }

//    /**
//     * @Route("/events/{id}")
//     * @Template
//     */
//    public function eventAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
//    {
//
//        $variables = [];
//
//        // Lets see if we need to create
//        if($id == 'new'){
//            $variables['resource'] = ['@id' => null,'id'=>'new'];
//        }
//        else{
//            $variables['resource'] = $commonGroundService->getResource('https://ac.huwelijksplanner.online/events/' . $id);
//        }
//
//        // If it is a delete action we can stop right here
//        if($request->query->get('action') == 'delete'){
//            $commonGroundService->deleteResource($variables['resource']);
//            return $this->redirect($this->generateUrl('app_ac_events'));
//        }
//
//        $variables['title'] = $translator->trans('event');
//        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('event');
//        $variables['organizations'] = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/organizations')["hydra:member"];
//
//        // Lets see if there is a post to procces
//        if ($request->isMethod('POST')) {
//
//            // Passing the variables to the resource
//            $resource = $request->request->all();
//            $resource['@id'] = $variables['resource']['@id'];
//            $resource['id'] = $variables['resource']['id'];
//
//            // If there are any sub data sources the need to be removed below in order to save the resource
//            // unset($resource['somedatasource'])
//
//            $variables['resource'] = $commonGroundService->saveResource($resource,'https://ac.huwelijksplanner.online/events/');
//        }
//
//        return $variables;
//    }
//
//    /**
//     * @Route("/schedules")
//     * @Template
//     */
//    public function schedulesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
//    {
//        $variables = [];
//        $variables['title'] = $translator->trans('schedules');
//        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('schedules');
//        $variables['resources'] = $commonGroundService->getResourceList('https://ac.huwelijksplanner.online/schedules')["hydra:member"];
//
//        return $variables;
//    }
//
//    /**
//     * @Route("/schedules/{id}")
//     * @Template
//     */
//    public function scheduleAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
//    {
//
//        $variables = [];
//
//        // Lets see if we need to create
//        if($id == 'new'){
//            $variables['resource'] = ['@id' => null,'id'=>'new'];
//        }
//        else{
//            $variables['resource'] = $commonGroundService->getResource('https://ac.huwelijksplanner.online/schedules/' . $id);
//        }
//
//        // If it is a delete action we can stop right here
//        if($request->query->get('action') == 'delete'){
//            $commonGroundService->deleteResource($variables['resource']);
//            return $this->redirect($this->generateUrl('app_ac_schedules'));
//        }
//
//        $variables['title'] = $translator->trans('schedule');
//        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('schedule');
//        $variables['organizations'] = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/organizations')["hydra:member"];
//
//        // Lets see if there is a post to procces
//        if ($request->isMethod('POST')) {
//
//            // Passing the variables to the resource
//            $resource = $request->request->all();
//            $resource['@id'] = $variables['resource']['@id'];
//            $resource['id'] = $variables['resource']['id'];
//
//            // If there are any sub data sources the need to be removed below in order to save the resource
//            // unset($resource['somedatasource'])
//
//            $variables['resource'] = $commonGroundService->saveResource($resource,'https://ac.huwelijksplanner.online/schedules/');
//        }
//
//        return $variables;
//    }

    /**
     * @Route("/calendars")
     * @Template
     */
    public function calendarsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {

        $variables = [];
        $variables['title'] = $translator->trans('calendars');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('calendars');
        $variables['resources'] =  $commonGroundService->getResourceList(['component'=>'arc', 'type'=>'calendars'])["hydra:member"];

        return $variables;
    }
    private function durationConverter($minutes){
        $diff = \DateInterval::createFromDateString($minutes." minutes");
        return $diff->format('P%yY%mM%dDT%hH%IM%sS');
    }

    /**
     * @Route("/calendars/{id}")
     * @Template
     */
    public function calendarAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource(['component'=>'arc','type'=>'calendars','id'=>$id]);
            return $this->redirect($this->generateUrl('app_arc_calendars'));
        }
        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'arc', 'type'=>'calendars','id'=>$id]);
            $variables['schedules'] = $commonGroundService->getResourceList(['component'=>'arc','type'=>'schedules'],['calendar.id'=>$id])['hydra:member'];
            $variables['events'] = $commonGroundService->getResourceList(['component'=>'arc','type'=>'events'],['calendar.id'=>$id])['hydra:member'];
            $variables['todos'] = $commonGroundService->getResourceList(['component'=>'arc','type'=>'todos'],['calendar.id'=>$id])['hydra:member'];
            $variables['journals'] = $commonGroundService->getResourceList(['component'=>'arc','type'=>'journals'],['calendar.id'=>$id])['hydra:member'];
            $variables['freebusies'] = $commonGroundService->getResourceList(['component'=>'arc','type'=>'freebusies'],['calendar.id'=>$id])['hydra:member'];
            $variables['alarms'] = $commonGroundService->getResourceList(['component'=>'arc','type'=>'alarms'],['event.calendar.id'=>$id])['hydra:member'];
            $variables['alarms'] = array_merge($variables['alarms'],$commonGroundService->getResourceList(['component'=>'arc','type'=>'alarms'],['todo.calendar.id'=>$id])['hydra:member']);
        }


        // If it is a delete action we can stop right here

        $variables['title'] = $translator->trans('calendar');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('calendar');

        if($id != 'new'){

        }

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            if(key_exists('schedule', $resource)){
                $schedule = $resource['schedule'];
                $schedule['calendar'] = $resource['@id'];
                if(in_array('id',$schedule)){
                    $schedule['@id'] = $schedule['id'];
                }
                $schedule = $commonGroundService->saveResource($schedule, ['component'=>'arc','type'=>'schedules']);
            }
            if(key_exists('event', $resource)){
                $event = $resource['event'];
                $event['calendar'] = $resource['@id'];
                if(in_array('id',$event)){
                    $event['@id'] = $event['id'];
                }
                $event = $commonGroundService->saveResource($event, ['component'=>'arc','type'=>'events']);
            }
            if(key_exists('todo', $resource)){
                $todo = $resource['todo'];
                $todo['calendar'] = $resource['@id'];
                if(in_array('id',$todo)){
                    $todo['@id'] = $todo['id'];
                }
                $todo = $commonGroundService->saveResource($todo, ['component'=>'arc','type'=>'todos']);
            }
            if(key_exists('journal', $resource)){
                $journal = $resource['journal'];
                $journal['calendar'] = $resource['@id'];
                if(in_array('id',$journal)){
                    $alarm['@id'] = $journal['id'];
                }
                $journal = $commonGroundService->saveResource($journal, ['component'=>'arc','type'=>'journals']);
            }
            if(key_exists('freeBusy', $resource)){
                $freeBusy = $resource['freeBusy'];
                $freeBusy['calendar'] = $resource['@id'];
                if(in_array('id',$freeBusy)){
                    $alarm['@id'] = $freeBusy['id'];
                }
                $freeBusy = $commonGroundService->saveResource($freeBusy, ['component'=>'arc','type'=>'freebusies']);
            }
            if(key_exists('alarm', $resource)){
                $alarm = $resource['alarm'];
                $alarm['calendar'] = $resource['@id'];
                if(in_array('id',$alarm)){
                    $alarm['@id'] = $alarm['id'];
                }
                $alarm['duration'] = $this->durationConverter($alarm['duration']);
                $alarm['trigger'] = $this->durationConverter($alarm['trigger']);
                $alarm['repeat'] = (int)$alarm['repeat'];
//                var_dump($alarm['trigger']);
//                die;
                if($alarm['event'] == ""){
                    unset($alarm['event']);
                }
                if($alarm['todo'] == ""){
                    unset($alarm['todo']);
                }
                $alarm = $commonGroundService->saveResource($alarm, ['component'=>'arc','type'=>'alarms']);
            }
            $variables['resource'] = $commonGroundService->saveResource($resource,['component'=>'arc','type'=>'calendars']);
        }

        return $variables;
    }
//
//    /**
//     * @Route("/freebusies")
//     * @Template
//     */
//    public function freebusiesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
//    {
//        $variables = [];
//        $variables['title'] = $translator->trans('freebusies');
//        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('freebusies');
//        $variables['resources'] = $commonGroundService->getResourceList('https://ac.huwelijksplanner.online/freebusies')["hydra:member"];
//
//        return $variables;
//    }
//
//    /**
//     * @Route("/freebusies/{id}")
//     * @Template
//     */
//    public function freebusyAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
//    {
//
//        $variables = [];
//
//        // Lets see if we need to create
//        if($id == 'new'){
//            $variables['resource'] = ['@id' => null,'id'=>'new'];
//        }
//        else{
//            $variables['resource'] = $commonGroundService->getResource('https://ac.huwelijksplanner.online/freebusies/' . $id);
//        }
//
//        // If it is a delete action we can stop right here
//        if($request->query->get('action') == 'delete'){
//            $commonGroundService->deleteResource($variables['resource']);
//            return $this->redirect($this->generateUrl('app_ac_freebusies'));
//        }
//
//        $variables['title'] = $translator->trans('freebusy');
//        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('freebusy');
//        $variables['organizations'] = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/organizations')["hydra:member"];
//
//        // Lets see if there is a post to procces
//        if ($request->isMethod('POST')) {
//
//            // Passing the variables to the resource
//            $resource = $request->request->all();
//            $resource['@id'] = $variables['resource']['@id'];
//            $resource['id'] = $variables['resource']['id'];
//
//            // If there are any sub data sources the need to be removed below in order to save the resource
//            // unset($resource['somedatasource'])
//
//            $variables['resource'] = $commonGroundService->saveResource($resource,'https://ac.huwelijksplanner.online/freebusies/');
//        }
//
//        return $variables;
//    }
//
//    /**
//     * @Route("/journals")
//     * @Template
//     */
//    public function journalsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
//    {
//        $variables = [];
//        $variables['title'] = $translator->trans('journals');
//        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('journals');
//        $variables['resources'] = $commonGroundService->getResourceList('https://ac.huwelijksplanner.online/journals')["hydra:member"];
//
//        return $variables;
//    }
//
//    /**
//     * @Route("/journals/{id}")
//     * @Template
//     */
//    public function journalAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
//    {
//
//        $variables = [];
//
//        // Lets see if we need to create
//        if($id == 'new'){
//            $variables['resource'] = ['@id' => null,'id'=>'new'];
//        }
//        else{
//            $variables['resource'] = $commonGroundService->getResource('https://ac.huwelijksplanner.online/journals/' . $id);
//        }
//
//        // If it is a delete action we can stop right here
//        if($request->query->get('action') == 'delete'){
//            $commonGroundService->deleteResource($variables['resource']);
//            return $this->redirect($this->generateUrl('app_ac_journals'));
//        }
//
//        $variables['title'] = $translator->trans('journal');
//        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('journal');
//        $variables['organizations'] = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/organizations')["hydra:member"];
//
//        // Lets see if there is a post to procces
//        if ($request->isMethod('POST')) {
//
//            // Passing the variables to the resource
//            $resource = $request->request->all();
//            $resource['@id'] = $variables['resource']['@id'];
//            $resource['id'] = $variables['resource']['id'];
//
//            // If there are any sub data sources the need to be removed below in order to save the resource
//            // unset($resource['somedatasource'])
//
//            $variables['resource'] = $commonGroundService->saveResource($resource,'https://ac.huwelijksplanner.online/journals/');
//        }
//
//        return $variables;
//    }
//
//    /**
//     * @Route("/todos")
//     * @Template
//     */
//    public function todosAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
//    {
//        $variables = [];
//        $variables['title'] = $translator->trans('todos');
//        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('todos');
//        $variables['resources'] = $commonGroundService->getResourceList('https://ac.huwelijksplanner.online/todos')["hydra:member"];
//
//        return $variables;
//    }
//
//    /**
//     * @Route("/todos/{id}")
//     * @Template
//     */
//    public function todoAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
//    {
//
//        $variables = [];
//
//        // Lets see if we need to create
//        if($id == 'new'){
//            $variables['resource'] = ['@id' => null,'id'=>'new'];
//        }
//        else{
//            $variables['resource'] = $commonGroundService->getResource('https://ac.huwelijksplanner.online/todos/' . $id);
//        }
//
//        // If it is a delete action we can stop right here
//        if($request->query->get('action') == 'delete'){
//            $commonGroundService->deleteResource($variables['resource']);
//            return $this->redirect($this->generateUrl('app_ac_todos'));
//        }
//
//        $variables['title'] = $translator->trans('todo');
//        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('todo');
//        $variables['organizations'] = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/organizations')["hydra:member"];
//
//        // Lets see if there is a post to procces
//        if ($request->isMethod('POST')) {
//
//            // Passing the variables to the resource
//            $resource = $request->request->all();
//            $resource['@id'] = $variables['resource']['@id'];
//            $resource['id'] = $variables['resource']['id'];
//
//            // If there are any sub data sources the need to be removed below in order to save the resource
//            // unset($resource['somedatasource'])
//
//            $variables['resource'] = $commonGroundService->saveResource($resource,'https://ac.huwelijksplanner.online/todos/');
//        }
//
//        return $variables;
//    }
//
//    /**
//     * @Route("/alarms")
//     * @Template
//     */
//    public function alarmsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
//    {
//        $variables = [];
//        $variables['title'] = $translator->trans('alarms');
//        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('alarms');
//        $variables['resources'] = $commonGroundService->getResourceList('https://ac.huwelijksplanner.online/alarms')["hydra:member"];
//
//        return $variables;
//    }
//
//    /**
//     * @Route("/alarms/{id}")
//     * @Template
//     */
//    public function alarmAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
//    {
//        $variables = [];
//
//        // Lets see if we need to create
//        if($id == 'new'){
//            $variables['resource'] = ['@id' => null,'id'=>'new'];
//        }
//        else{
//            $variables['resource'] = $commonGroundService->getResource('https://ac.huwelijksplanner.online/alarms/' . $id);
//        }
//
//        // If it is a delete action we can stop right here
//        if($request->query->get('action') == 'delete'){
//            $commonGroundService->deleteResource($variables['resource']);
//            return $this->redirect($this->generateUrl('app_ac_alarms'));
//        }
//
//        $variables['title'] = $translator->trans('alarm');
//        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('alarm');
//        $variables['organizations'] = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/organizations')["hydra:member"];
//
//        // Lets see if there is a post to procces
//        if ($request->isMethod('POST')) {
//
//            // Passing the variables to the resource
//            $resource = $request->request->all();
//            $resource['@id'] = $variables['resource']['@id'];
//            $resource['id'] = $variables['resource']['id'];
//
//            // If there are any sub data sources the need to be removed below in order to save the resource
//            // unset($resource['somedatasource'])
//
//            $variables['resource'] = $commonGroundService->saveResource($resource,'https://ac.huwelijksplanner.online/alarms/');
//        }
//
//        return $variables;
//    }

}
