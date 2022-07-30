<?php

declare(strict_types=1);

namespace Baraja\StructuredApi\Doc\Descriptor;


use Baraja\StructuredApi\Doc\Helpers;
use Baraja\StructuredApi\Endpoint;
use Nette\Utils\Strings;

final class EndpointInfo
{
	/** @var array<int, string> */
	private static array $prefixes = ['action', 'create', 'post', 'delete', 'put', 'patch'];

	private string $route;

	private string $class;

	private Endpoint $endpoint;

	private \ReflectionClass $reflection;


	public function __construct(string $route, string $class, Endpoint $endpoint)
	{
		$this->reflection = new \ReflectionClass($endpoint);
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
			if (preg_match($pattern, $method->getName(), $match) === 1) {
				$return[] = new ApiAction(
					methodName: $match[0],
					method: $match[1],
					name: Strings::firstLower($match[2]),
					comment: (string) $method->getDocComment(),
					parameters: $method->getParameters(),
					returnType: $method->getReturnType()?->getName(),
				);
			}
		}

		return $return;
	}


	public function getComment(): ?string
	{
		$comment = trim((string) $this->reflection->getDocComment());
		if ($comment !== '') {
			return Helpers::normalizeComment($comment);
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
