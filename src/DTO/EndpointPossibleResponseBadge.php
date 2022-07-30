<?php

declare(strict_types=1);

namespace Baraja\StructuredApi\Doc\DTO;


final class EndpointPossibleResponseBadge
{
	/**
	 * @param array<int, EntityResponsePropertyResponse> $properties
	 */
	public function __construct(
		public int $httpCode = 200,
		public ?string $message = null,
		public array $properties = [],
		public ?string $typescriptDefinition = null,
	) {
	}
}
