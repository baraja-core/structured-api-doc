<?php

declare(strict_types=1);

namespace Baraja\StructuredApi\Doc\Descriptor;


use Baraja\StructuredApi\Doc\Helpers;

final class ApiAction
{

	/** @var string[] */
	private const METHOD_TO_HTTP_METHOD_REWRITES = ['action' => 'GET'];

	/** @var string */
	private $methodName;

	/** @var string */
	private $method;

	/** @var string */
	private $name;

	/** @var string|null */
	private $comment;

	/** @var \ReflectionParameter[] */
	private $parameters;


	/**
	 * @param \ReflectionParameter[] $parameters
	 */
	public function __construct(string $methodName, string $method, string $name, ?string $comment, array $parameters)
	{
		$this->methodName = $methodName;
		$this->method = $method;
		$this->name = $name;
		$this->comment = $comment === null ? null : Helpers::normalizeComment($comment);
		$this->parameters = $parameters;
	}


	public function getMethodName(): string
	{
		return $this->methodName;
	}


	public function getMethod(): string
	{
		return $this->method;
	}


	public function getHttpMethod(): string
	{
		return strtoupper(self::METHOD_TO_HTTP_METHOD_REWRITES[$method = strtolower($this->getMethod())] ?? $method);
	}


	public function getName(): string
	{
		return $this->name;
	}


	public function getRoute(): string
	{
		return Helpers::formatPresenterNameToUri($this->getName());
	}


	public function getComment(): ?string
	{
		return $this->comment;
	}


	/**
	 * @return \ReflectionParameter[]
	 */
	public function getParameters(): array
	{
		return $this->parameters;
	}
}
