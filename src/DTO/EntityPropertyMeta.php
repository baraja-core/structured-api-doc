<?php

declare(strict_types=1);

namespace Baraja\StructuredApi\Doc\DTO;


final class EntityPropertyMeta
{
	/**
	 * @param int<0, max> $position
	 * @param non-empty-string $name
	 * @param class-string|non-empty-string $type
	 * @param array<int, self> $children
	 */
	public function __construct(
		private int $position,
		private string $name,
		private string $type,
		private ?string $default,
		private bool $required,
		private ?string $description,
		private array $children,
	) {
	}


	public function toEntity(): EndpointParameterResponse
	{
		return new EndpointParameterResponse(
			position: $this->position,
			name: $this->name,
			type: $this->type,
			default: $this->default,
			required: $this->required,
			description: $this->description,
		);
	}


	/**
	 * @return int<0, max>
	 */
	public function getPosition(): int
	{
		return $this->position;
	}


	/**
	 * @return non-empty-string
	 */
	public function getName(): string
	{
		return $this->name;
	}


	/**
	 * @return class-string|string
	 */
	public function getType(): string
	{
		return $this->type;
	}


	public function getDefault(): ?string
	{
		return $this->default;
	}


	public function isRequired(): bool
	{
		return $this->required;
	}


	public function getDescription(): ?string
	{
		return $this->description;
	}


	/**
	 * @return array<int, self>
	 */
	public function getChildren(): array
	{
		return $this->children;
	}
}
