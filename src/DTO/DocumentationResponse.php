<?php

declare(strict_types=1);

namespace Baraja\StructuredApi\Doc\DTO;


final class DocumentationResponse
{
	/**
	 * @param array<int, EndpointActionResponse> $actions
	 */
	public function __construct(
		public string $route,
		public string $class,
		public string $name,
		public ?string $description,
		public bool $public,
		public array $actions,
	) {
	}
}
