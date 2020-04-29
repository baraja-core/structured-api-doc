<?php

declare(strict_types=1);

namespace Baraja\StructuredApi\Doc\Descriptor;


use Baraja\StructuredApi\Doc\Helpers;
use Baraja\StructuredApi\Endpoint;
use Nette\Utils\Strings;

final class EndpointInfo
{

	/** @var string[] */
	private static $prefixes = ['action', 'create', 'post', 'delete', 'put', 'patch'];

	/** @var string */
	private $route;

	/** @var string */
	private $class;

	/** @var Endpoint */
	private $endpoint;

	/** @var \ReflectionClass */
	private $reflection;


	/**
	 * @param string $route
	 * @param string $class
	 * @param Endpoint $endpoint
	 * @throws \ReflectionException
	 */
	public function __construct(string $route, string $class, Endpoint $endpoint)
	{
		$this->route = $route;
		$this->class = $class;
		$this->endpoint = $endpoint;
		$this->reflection = new \ReflectionClass($endpoint);
	}


	/**
	 * @return ApiAction[]
	 */
	public function getActionMethods(): array
	{
		$return = [];
		$pattern = '/^(?<method>' . implode('|', self::$prefixes) . ')(?<name>.+)$/';

		foreach ($this->reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
			if (preg_match($pattern, $method->getName(), $match)) {
				$return[] = new ApiAction($match[0], $match[1], Strings::firstLower($match[2]), $method->getDocComment() ?: null, $method->getParameters());
			}
		}

		return $return;
	}


	public function getComment(): ?string
	{
		if (($comment = $this->reflection->getDocComment() ?: null) !== null) {
			return Helpers::normalizeComment((string) $comment);
		}

		return null;
	}


	/**
	 * @return string
	 */
	public function getRoute(): string
	{
		return $this->route;
	}


	/**
	 * @return string
	 */
	public function getClass(): string
	{
		return $this->class;
	}


	/**
	 * @return Endpoint
	 */
	public function getEndpoint(): Endpoint
	{
		return $this->endpoint;
	}
}