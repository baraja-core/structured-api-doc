<?php

declare(strict_types=1);

namespace Baraja\StructuredApi\Doc\DTO;


class EndpointParameterResponse
{
	/**
	 * @param int<0, max> $position
	 * @param non-empty-string $name
	 * @param class-string|non-empty-string $type
	 */
	final public function __construct(
		public int $position,
		public string $name,
		public string $type,
		public mixed $default,
		public bool $required,
		public ?string $description,
	) {
	}
}
