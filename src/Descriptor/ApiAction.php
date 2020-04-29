<?php

declare(strict_types=1);

namespace Baraja\StructuredApi\Doc\Descriptor;


use Baraja\StructuredApi\Doc\Helpers;
use ShopUp\Router\Helper;

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
	 * @param string $methodName
	 * @param string $method
	 * @param string $name
	 * @param string|null $comment
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


	/**
	 * @return string
	 */
	public function getMethodName(): string
	{
		return $this->methodName;
	}


	/**
	 * @return string
	 */
	public function getMethod(): string
	{
		return $this->method;
	}


	/**
	 * @return string
	 */
	public function getHttpMethod(): string
	{
		return strtoupper(self::METHOD_TO_HTTP_METHOD_REWRITES[$method = strtolower($this->getMethod())] ?? $method);
	}


	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}


	/**
	 * @return string
	 */
	public function getRoute(): string
	{
		return Helper::formatPresenterNameToUri($this->getName());
	}


	/**
	 * @return string|null
	 */
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