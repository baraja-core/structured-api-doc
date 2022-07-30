<?php

declare(strict_types=1);

namespace Baraja\StructuredApi\Doc;


use Baraja\StructuredApi\Doc\DTO\EntityResponsePropertyResponse;
use Nette\Utils\Strings;

final class TypeScriptResponseHydration
{
	private const PhpTypeToTypescriptDefinition = [
		'int' => 'number',
		'float' => 'number',
		'bool' => 'boolean',
		'string' => 'string',
	];


	/**
	 * @param array<int, EntityResponsePropertyResponse> $properties
	 * @param array<int, string> $usedInterfaces
	 */
	public static function hydrateDefinition(array $properties, array $usedInterfaces = []): string
	{
		if ($properties === []) {
			return $usedInterfaces === [] ? '// no response' : '';
		}

		$return = '';
		$baseTypes = [];

		foreach ($properties as $property) {
			if ($property->children !== []) {
				$interfaceName = Strings::firstUpper($property->name) . 'Response';
				$return .= self::createInterface(
					$interfaceName,
					self::hydrateDefinition($property->children, array_merge($usedInterfaces, [$interfaceName])),
				);
				$baseTypes[] = self::renderProperty($property, $interfaceName);
			} else {
				$baseTypes[] = self::renderProperty($property);
			}
		}

		$return .= $usedInterfaces === []
			? self::createInterface('Response', implode("\n", $baseTypes))
			: implode("\n", $baseTypes);

		return trim($return);
	}


	private static function createInterface(string $name, string $content = ''): string
	{
		return sprintf(
			'export interface %s {%s}' . "\n\n",
			Strings::firstUpper($name),
			$content !== '' ? "\n  " . implode("\n  ", explode("\n", trim($content))) . "\n" : '',
		);
	}


	private static function renderProperty(EntityResponsePropertyResponse $property, ?string $interface = null): string
	{
		$type = (string) preg_replace('/\|null$/', '', (string) $property->type);
		$realType = $interface ?? self::translatePhpTypeToTypescriptDefinition($type);

		$description = '';
		if ($property->description !== null && $property->description !== '') {
			$description .= $property->description;
		}
		if (($realType === 'unknown' || $realType === 'unknown[]') && $property->annotation !== null) {
			$description .= '  ' . $property->annotation;
		}

		return sprintf(
			'%s%s: %s;%s',
			$property->name,
			$property->nullable ? '?' : '',
			$realType,
			trim($description) !== '' ? sprintf(' // %s', trim(str_replace("\n", ' ', $description))) : '',
		);
	}


	private static function translatePhpTypeToTypescriptDefinition(string $type): string
	{
		if (isset(self::PhpTypeToTypescriptDefinition[$type])) {
			return self::PhpTypeToTypescriptDefinition[$type];
		}
		if ($type === 'array') {
			return 'unknown[]';
		}

		return 'unknown';
	}
}
