<?php

declare(strict_types=1);

namespace Baraja\StructuredApi\Doc;


use Baraja\Serializer\Serializer;
use Baraja\StructuredApi\ApiManager;
use Baraja\StructuredApi\Doc\Descriptor\EndpointInfo;
use Baraja\StructuredApi\Endpoint;
use Baraja\Url\Url;
use Nette\DI\Container;
use Nette\Security\User;

final class Documentation
{
	private const ContentTypes = [
		'js' => 'application/javascript',
		'css' => 'text/css',
		'ico' => 'image/x-icon',
		'txt' => 'text/plain',
		'map' => 'text/plain',
	];

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
			echo file_get_contents(__DIR__ . '/permissionDenied.html');
			die;
		}
		$url = Url::get();
		$urlParts = explode('/', $url->getRelativeUrl(), 2);
		$path = trim($urlParts[1] ?? '', '/');

		if ($path === '') {
			echo preg_replace(
				'~(src|href)="/~',
				sprintf('$1="%s/api-documentation/', $url->getBaseUrl()),
				(string) file_get_contents(__DIR__ . '/../out/index.html'),
			);
			echo sprintf('<div id="brj-endpoint-url" style="display:none">%s/%s/api</div>', $url->getBaseUrl(), $urlParts[0]);
			die;
		}
		if ($path !== 'api') {
			if (str_contains($path, '..')) {
				echo 'Bad request.';
			} else {
				$assetPath = sprintf('%s/out/%s', dirname(__DIR__), $path);
				if (is_file($assetPath)) {
					$extension = pathinfo($assetPath, PATHINFO_EXTENSION);
					header(sprintf('Content-Type: %s', self::ContentTypes[$extension] ?? self::ContentTypes['js']));
					echo file_get_contents($assetPath);
				} else {
					echo sprintf('File "%s" does not exist.', htmlspecialchars($path));
				}
			}
			die;
		}

		$endpointInfos = [];
		$errors = [];

		$endpoints = $this->apiManager->getEndpoints();
		foreach ($endpoints as $route => $endpointClass) {
			/** @var Endpoint $endpoint */
			$endpoint = $this->container->getByType($endpointClass);

			try {
				$endpointInfos[] = new EndpointInfo($route, $endpointClass, $endpoint);
			} catch (\ReflectionException $e) {
				$errors[] = $e->getMessage();
			}
		}

		header('Content-Type: application/json; charset=utf-8');
		echo json_encode(
			Serializer::get()->serialize([
				'endpoints' => $this->renderer->render(new DocumentationInfo($endpoints, $endpointInfos)),
				'errors' => $errors,
			]),
			JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE,
		);
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
