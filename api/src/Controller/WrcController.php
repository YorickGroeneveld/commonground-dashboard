<?php

// src/Controller/DashboardController.php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

use App\Service\CommonGroundService;

/**
 * Class DashboardController
 * @package App\Controller
 * @Route("/wrc")
 */
class WrcController extends AbstractController
{
    /**
     * @Route("/")
     * @Template
     */
    public function indexAction(Request $request, CommonGroundService $commonGroundService)
    {
    	$variables = [];
    	$variables['title'] = $translator->trans('webresources');
    	$variables['subtitle'] = $translator->trans('the webresources consist of all items that are needed to render the application web interface');
    	
    	return $variables;
    }

    /**
     * @Route("/templates")
     * @Template
     */
    public function templatesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
    	$variables = [];
    	$variables['title'] = $translator->trans('templates');
    	$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('templates');
    	$variables['resources'] = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/templates')["hydra:member"];
    	
    	return $variables;
    }

    /**
     * @Route("/templates/{id}")
     * @Template
     */
    public function templateAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
    	$variables = [];
    	$variables['title'] = $translator->trans('template');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('template');
    	
    	// Lets see if we need to create
    	if($id == 'new'){
    		$variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
    	}
    	else{
    		$variables['resource'] = $commonGroundService->getResource('https://wrc.huwelijksplanner.online/templates/'.$id);
    	}
    	
    	// Lets see if there is a post to procces
    	if ($request->isMethod('POST')) {
    		
    		// Passing the variables to the resource
    		$resource = $request->request->all();
    		$resource['@id'] = $variables['resource']['@id'];
    		$resource['id'] = $variables['resource']['id'];
    		
    		// If there are any sub data sources the need to be removed below in order to save the resource
    		// unset($resource['somedatasource'])
    		
    		$variables['resource'] = $commonGroundService->saveResource($resource,'https://wrc.huwelijksplanner.online/organizations/');
    	}
    	
    	return $variables;
    }
    
    
    
    /**
     * @Route("/organizations")
     * @Template
     */
    public function organizationsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
    	$variables = [];
    	$variables['title'] = $translator->trans('organizations');
    	$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('v');
    	$variables['resources'] = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/organizations')["hydra:member"];
    	
    	return $variables;
    }
    
    /**
     * @Route("/organizations/{id}")
     * @Template
     */
    public function organizationAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
    	$variables = [];
    	$variables['title'] = $translator->trans('organization');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('organization');
    	
    	// Lets see if we need to create
    	if($id == 'new'){
    		$variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
    	}
    	else{
    		$variables['resource'] = $commonGroundService->getResource('https://wrc.huwelijksplanner.online/organizations/'.$id);
    	}
    	
    	// Lets see if there is a post to procces
    	if ($request->isMethod('POST')) {
    		
    		// Passing the variables to the resource
    		$resource = $request->request->all();
    		$resource['@id'] = $variables['resource']['@id'];
    		$resource['id'] = $variables['resource']['id'];
    		
    		// If there are any sub data sources the need to be removed below in order to save the resource
    		// unset($resource['somedatasource'])
    		
    		$variables['resource'] = $commonGroundService->saveResource($resource,'https://wrc.huwelijksplanner.online/organizations/');
    	}
    	
    	return $variables;
    }
}
