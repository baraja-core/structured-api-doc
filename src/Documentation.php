<?php

declare(strict_types=1);

namespace Baraja\StructuredApi\Doc;


use Baraja\StructuredApi\ApiManager;
use Baraja\StructuredApi\Doc\Descriptor\EndpointInfo;

final class Documentation
{

	/** @var ApiManager */
	private $apiManager;

	/** @var Renderer */
	private $renderer;


	/**
	 * @param ApiManager $apiManager
	 */
	public function __construct(ApiManager $apiManager)
	{
		$this->apiManager = $apiManager;
		$this->renderer = new Renderer;
	}


	public function run(): void
	{
		$endpointInfos = [];
		$errors = [];

		foreach ($endpoints = $this->apiManager->getEndpoints() as $route => $endpointClass) {
			try {
				$endpointInfos[] = new EndpointInfo($route, $endpointClass, $this->apiManager->createEndpointInstance($endpointClass, []));
			} catch (\ReflectionException $e) {
				$errors[] = $e->getMessage();
			}
		}

		$this->renderer->render(new DocumentationInfo($endpoints, $endpointInfos), $errors);
		die;
	}
}