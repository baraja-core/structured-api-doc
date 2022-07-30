<?php

declare(strict_types=1);

namespace Baraja\StructuredApi\Doc\DTO;


final class EndpointAggregatedParameterResponse extends EndpointParameterResponse
{
	/** @var array<int, EndpointParameterResponse> */
	public array $objectProperties = [];
}
