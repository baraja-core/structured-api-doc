<?php

declare(strict_types=1);

namespace Baraja\StructuredApi\Doc\DTO;


final class EntityResponsePropertyResponse
{
	/**
	 * @param array<int, EntityResponsePropertyResponse> $children
	 */
	public function __construct(
		public string $name,
		public ?string $type = null,
		public ?string $description = null,
		public ?string $annotation = null,
		public bool $nullable = false,
		public array $children = [],
	) {
	}
}
