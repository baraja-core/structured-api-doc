<?php

declare(strict_types=1);

namespace Baraja\StructuredApi\Doc;


use Baraja\StructuredApi\ApiManager;
use Baraja\StructuredApi\Doc\Descriptor\EndpointInfo;
use Latte\Engine;
use Nette\Security\User;

final class Documentation
{

	/** @var ApiManager */
	private $apiManager;

	/** @var User|null */
	private $user;

	/** @var bool */
	private $loggedIn = false;

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


	/**
	 * Check permission and render documentation page or error.
	 */
	public function run(): void
	{
		if ($this->isLoggedIn() === false) {
			(new Engine)->render(__DIR__ . '/permissionDenied.latte');
			die;
		}

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


	/**
	 * @param User $user
	 * @internal for DIC
	 */
	public function injectUser(User $user): void
	{
		$this->user = $user;
	}


	/**
	 * Check if current user can show documentation.
	 * In case of Nette User current identity must be logged in and be in role "admin" or "api-developer".
	 *
	 * @return bool
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
	 *
	 * @param bool $loggedIn
	 */
	public function setLoggedIn(bool $loggedIn): void
	{
		$this->loggedIn = $loggedIn;
	}
}
