<?php

// src/Service/HuwelijkService.php

namespace App\Service;

use App\Service\CommonGroundService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ZgwService
{
	private $params;
	private $commonGroundService;
	
	public function __construct(ParameterBagInterface $params, CommonGroundService $commonGroundService)
	{
		$this->params = $params;
		$this->commonGroundService = $commonGroundService;
	}
	
	/*
	 * Get a single resource from a common ground componant
	 */
	public function getResourceList($url, $query = [], $force = false, $async = false, $autowire = false)
	{
		$this->commonGroundService->setHeader('Authorization','Bearer '.$this->getJwtToken());
		$this->commonGroundService->setHeader('Accept','application/json');	
		return $this->commonGroundService->getResourceList($url, $query, $force, $async, $autowire );
	}
	
	/*
	 * Get a single resource from a common ground componant
	 */
	public function getResource($url, $query = [], $force = false, $async = false, $autowire = false)
	{
		$this->commonGroundService->setHeader('Authorization','Bearer '.$this->getJwtToken());
		$this->commonGroundService->setHeader('Accept','application/json');	
		return $this->commonGroundService->getResource($url, $query , $force, $async, $autowire );
	}
	
	/*
	 * Get a single resource from a common ground componant
	 */
	public function updateResource($resource, $url = null, $async = false, $autowire = false)
	{
		$this->commonGroundService->setHeader('Authorization','Bearer '.$this->getJwtToken());
		$this->commonGroundService->setHeader('Accept','application/json');	
		return $this->commonGroundService->updateResource($resource, $url, $async, $autowire );
	}
	
	/*
	 * Create a sresource on a common ground component
	 */
	public function createResource($resource, $url = null, $async = false, $autowire = false)
	{		
		$this->commonGroundService->setHeader('Authorization','Bearer '.$this->getJwtToken());
		$this->commonGroundService->setHeader('Accept','application/json');	
		return $this->commonGroundService->createResource($resource, $url, $async, $autowire );
	}
	
	
	/*
	 * Delete a single resource from a common ground component
	 */
	public function deleteResource($resource, $url = null, $async = false, $autowire = false)
	{
		$this->commonGroundService->setHeader('Authorization','Bearer '.$this->getJwtToken());
		$this->commonGroundService->setHeader('Accept','application/json');	
		return $this->commonGroundService->deleteResource($resource, $url, $async, $autowire );
	}
	
	/*
	 * The save fucntion should only be used by applications that can render flashes
	 */
	public function saveResource($resource, $endpoint = false, $autowire = false)
	{
		$this->commonGroundService->setHeader('Authorization','Bearer '.$this->getJwtToken());
		$this->commonGroundService->setHeader('Accept','application/json');	
		return $this->commonGroundService->saveResource($resource, $endpoint, $autowire );
	}
	
	
	/*
	 * Get the current application from the wrc
	 */
	public function getJwtToken()
	{
		$clientId = $this->params->get('app_commonground_zgw_clientId');		
		$secret = $this->params->get('app_commonground_zgw_secret');
		
		$userId = "";
		$userRepresentation = '';
		
		// Create token header as a JSON string
		$header = json_encode(['typ' => 'JWT', 'alg' => 'HS256', 'client_identifier' => $clientId]);
		
		// Create token payload as a JSON string
		$payload = json_encode(['iss' => $clientId, 'client_id' =>$clientId, "user_id" => $userId,"user_representation" => $userRepresentation, "iat" => time()]);
		
		// Encode Header to Base64Url String
		$base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
		
		// Encode Payload to Base64Url String
		$base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
		
		// Create Signature Hash
		$signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
		
		// Encode Signature to Base64Url String
		$base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
		
		// Return JWT				
		return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
	}
	
}
