<?php

declare(strict_types=1);

namespace Baraja\StructuredApi\Doc;


use Baraja\StructuredApi\Doc\Descriptor\EndpointInfo;

final class DocumentationInfo
{

	/**
	 * Simple list of all endpoints (route => className)
	 *
	 * @var string[]
	 */
	private $endpoints;

	/** @var EndpointInfo[] */
	private $endpointsInfo;


	/**
	 * @param string[] $endpoints
	 * @param EndpointInfo[] $endpointsInfo
	 */
	public function __construct(array $endpoints, array $endpointsInfo)
	{
		$this->endpoints = $endpoints;
		$this->endpointsInfo = $endpointsInfo;
	}


	/**
	 * @return string[]
	 */
	public function getEndpoints(): array
	{
		return $this->endpoints;
	}


	/**
	 * @return EndpointInfo[]
	 */
	public function getEndpointsInfo(): array
	{
		return $this->endpointsInfo;
	}
}
