<?php

declare(strict_types=1);

namespace Baraja\StructuredApi\Doc;


use Baraja\StructuredApi\Doc\Descriptor\EndpointInfo;

final class DocumentationInfo
{
	/**
	 * Simple list of all endpoints (route => className)
	 *
	 * @var array<string, class-string>
	 */
	private array $endpoints;

	/** @var array<int, EndpointInfo> */
	private array $endpointsInfo;


	/**
	 * @param array<string, class-string> $endpoints
	 * @param array<int, EndpointInfo> $endpointsInfo
	 */
	public function __construct(array $endpoints, array $endpointsInfo)
	{
		$this->endpoints = $endpoints;
		$this->endpointsInfo = $endpointsInfo;
	}


	/**
	 * @return array<string, class-string>
	 */
	public function getEndpoints(): array
	{
		return $this->endpoints;
	}


	/**
	 * @return array<int, EndpointInfo>
	 */
	public function getEndpointsInfo(): array
	{
		return $this->endpointsInfo;
	}
}
