<?php

declare(strict_types=1);

namespace Baraja\StructuredApi\Doc\DTO;


final class EndpointActionResponse
{
	/**
	 * @param array<int, string> $roles
	 * @param array<int, string> $throws
	 * @param EndpointParameterResponse[] $parameters
	 * @param EndpointPossibleResponseBadge[] $responses
	 */
	public function __construct(
		public string $name,
		public string $method,
		public string $route,
		public string $httpMethod,
		public string $methodName,
		public ?string $description,
		public array $roles = [],
		public array $throws = [],
		public array $parameters = [],
		public ?string $parametersDeclaringType = null,
		public ?string $returnType = null,
		public array $responses = [],
	) {
	}
}
