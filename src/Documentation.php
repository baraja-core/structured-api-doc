<?php

declare(strict_types=1);

namespace Baraja\StructuredApi\Doc;


use Baraja\StructuredApi\ApiManager;
use Baraja\StructuredApi\Doc\Descriptor\EndpointInfo;
use Baraja\StructuredApi\Endpoint;
use Latte\Engine;
use Nette\DI\Container;
use Nette\Security\User;

final class Documentation
{
	private ApiManager $apiManager;

	private Container $container;

	private ?User $user;

	private bool $loggedIn = false;

	private Renderer $renderer;


	public function __construct(ApiManager $apiManager, Container $container)
	{
		$this->apiManager = $apiManager;
		$this->container = $container;
		$this->renderer = new Renderer;
	}


	/**
	 * Check permission and render documentation page or error.
	 */
	public function run(): void
	{
		if ($this->isLoggedIn() === false && Helpers::isLocalRequest() === false) {
			(new Engine)->render(__DIR__ . '/permissionDenied.latte');
			die;
		}

		$endpointInfos = [];
		$errors = [];

		foreach ($endpoints = $this->apiManager->getEndpoints() as $route => $endpointClass) {
			/** @var Endpoint $endpoint */
			$endpoint = $this->container->getByType($endpointClass);

			try {
				$endpointInfos[] = new EndpointInfo($route, $endpointClass, $endpoint);
			} catch (\ReflectionException $e) {
				$errors[] = $e->getMessage();
			}
		}

		$this->renderer->render(new DocumentationInfo($endpoints, $endpointInfos), $errors);
		die;
	}


	/**
	 * @internal for DIC
	 */
	public function injectUser(User $user): void
	{
		$this->user = $user;
	}


	/**
	 * Check if current user can show documentation.
	 * In case of Nette User current identity must be logged in and be in role "admin" or "api-developer".
	 */
	public function isLoggedIn(): bool
	{
		if ($this->loggedIn === true) {
			return true;
		}

		if ($this->user !== null) {
			return $this->user->isLoggedIn() && ($this->user->isInRole('admin') || $this->user->isInRole('api-developer'));
		}

		return false;
	}


	/**
	 * Mark current request as logged in (for minimal dependency).
	 */
	public function setLoggedIn(bool $loggedIn): void
	{
		$this->loggedIn = $loggedIn;
	}
}
