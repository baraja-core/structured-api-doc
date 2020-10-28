<?php

declare(strict_types=1);

namespace Baraja\StructuredApi\Doc\Descriptor;


use Baraja\StructuredApi\Doc\Helpers;
use Baraja\StructuredApi\Endpoint;
use Nette\Utils\Strings;

final class EndpointInfo
{

	/** @var string[] */
	private static array $prefixes = ['action', 'create', 'post', 'delete', 'put', 'patch'];

	private string $route;

	private string $class;

	private Endpoint $endpoint;

	private \ReflectionClass $reflection;


	public function __construct(string $route, string $class, Endpoint $endpoint)
	{
		try {
			$this->reflection = new \ReflectionClass($endpoint);
		} catch (\ReflectionException $e) {
			throw new \InvalidArgumentException('Endpoint "' . \get_class($endpoint) . '" is invalid: ' . $e->getMessage(), $e->getCode(), $e);
		}

		$this->route = $route;
		$this->class = $class;
		$this->endpoint = $endpoint;
	}


	/**
	 * @return ApiAction[]
	 */
	public function getActionMethods(): array
	{
		$pattern = '/^(?<method>' . implode('|', self::$prefixes) . ')(?<name>.+)$/';
		$return = [];
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


	public function getRoute(): string
	{
		return $this->route;
	}


	public function getClass(): string
	{
		return $this->class;
	}


	public function getEndpoint(): Endpoint
	{
		return $this->endpoint;
	}
}
