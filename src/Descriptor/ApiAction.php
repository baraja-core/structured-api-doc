<?php

declare(strict_types=1);

namespace Baraja\StructuredApi\Doc\Descriptor;


use Baraja\StructuredApi\Doc\Helpers;

final class ApiAction
{
	private const MethodToHttpMethodRewrites = ['action' => 'GET'];

	private string $methodName;

	private string $method;

	private string $name;

	private ?string $comment;

	/** @var \ReflectionParameter[] */
	private array $parameters;

	private ?string $returnType;


	/**
	 * @param \ReflectionParameter[] $parameters
	 */
	public function __construct(
		string $methodName,
		string $method,
		string $name,
		string $comment,
		array $parameters,
		?string $returnType,
	) {
		$this->methodName = $methodName;
		$this->method = $method;
		$this->name = $name;
		$this->comment = $comment !== '' ? Helpers::normalizeComment($comment) : null;
		$this->parameters = $parameters;
		$this->returnType = $returnType;
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
		$method = strtolower($this->getMethod());

		return strtoupper(self::MethodToHttpMethodRewrites[$method] ?? $method);
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


	public function getReturnType(): ?string
	{
		return $this->returnType;
	}
}
