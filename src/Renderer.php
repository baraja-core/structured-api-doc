<?php

declare(strict_types=1);

namespace Baraja\StructuredApi\Doc;


use Baraja\StructuredApi\Doc\Descriptor\ApiAction;
use Baraja\StructuredApi\Doc\DTO\EntityPropertyMeta;
use Latte\Engine;
use Nette\Utils\Strings;

final class Renderer
{
	private const SCALAR_TYPES = ['string' => 1, 'bool' => 1, 'int' => 1, 'float' => 1, 'array' => 1, 'null' => 1];


	/**
	 * @param string[] $errors
	 */
	public function render(DocumentationInfo $documentation, array $errors = []): void
	{
		$structure = [];
		foreach ($documentation->getEndpointsInfo() as $endpoint) {
			$route = $endpoint->getRoute();
			$comment = $endpoint->getComment();
			$name = $comment === null
				? null
				: Helpers::findCommentAnnotation($comment, 'endpointName');

			$actions = [];
			foreach ($endpoint->getActionMethods() as $action) {
				$actions[] = $this->processAction($action);
			}

			$structure[] = [
				'route' => $route,
				'class' => $endpoint->getClass(),
				'name' => $name ?? Strings::firstUpper(str_replace('-', ' ', $route)),
				'description' => $comment === null ? null : Helpers::findCommentDescription($comment),
				'public' => $comment !== null && preg_match('/@public(?:$|\s|\n)/', $comment) === 1,
				'actions' => $actions,
			];
		}

		usort($structure, static fn(array $a, array $b): int => strcmp($a['route'], $b['route']));

		(new Engine)->render(__DIR__ . '/basic.latte', [
			'documentation' => $documentation,
			'errors' => $errors,
			'structure' => $structure,
		]);
	}


	/**
	 * @return array{
	 *    name: string,
	 *    method: string,
	 *    route: string,
	 *    httpMethod: string,
	 *    methodName: string,
	 *    description: string|null,
	 *    roles: array<int, string>|null,
	 *    throws: array<int, string>,
	 *    parameters: array<int, array{
	 *        position: int,
	 *        name: non-empty-string,
	 *        type: string,
	 *        default: mixed|null,
	 *        required: bool,
	 *        description: string|null
	 *     }>
	 * }
	 */
	private function processAction(ApiAction $action): array
	{
		$throws = [];
		$comment = $action->getComment();
		if ($comment !== null) {
			foreach (Helpers::findAllCommentAnnotations($comment, 'throws') as $throwItem) {
				$throws[] = explode('|', $throwItem);
			}
			$throws = array_merge([], ...$throws);
		}

		/** @phpstan-ignore-next-line */
		$roles = $comment !== null ? \Baraja\StructuredApi\Helpers::parseRolesFromComment($comment) : [];

		return [
			'name' => $action->getName(),
			'method' => $action->getMethod(),
			'route' => $action->getRoute(),
			'httpMethod' => $action->getHttpMethod(),
			'methodName' => $action->getMethodName(),
			'description' => $comment === null ? null : Helpers::findCommentDescription($comment),
			'roles' => $roles,
			'throws' => $throws,
			'parameters' => $this->processParameters($comment, $action->getParameters()),
		];
	}


	/**
	 * @param array<int, \ReflectionParameter> $parameters
	 * @return array<int, array{
	 *    position: int,
	 *    name: non-empty-string,
	 *    type: non-empty-string,
	 *    default: mixed|null,
	 *    required: bool,
	 *    description: string|null
	 * }>
	 */
	private function processParameters(?string $comment, array $parameters): array
	{
		$return = [];
		foreach ($parameters as $parameter) {
			$type = $parameter->getType();
			$typeName = $type?->getName();
			$enumValues = [];
			if (
				$typeName !== null
				&& $typeName !== 'string'
				&& $typeName !== 'int'
				&& \class_exists($typeName) === true
			) {
				if (is_subclass_of($typeName, \UnitEnum::class)) {
					$enumValues = array_map(static fn(\UnitEnum $case): string => htmlspecialchars($case->value ?? $case->name), $typeName::cases());
				} else {
					return array_map(
						static fn(EntityPropertyMeta $meta): array => $meta->toArray(),
						$this->processEntityProperties($typeName),
					);
				}
			}
			try {
				$default = $parameter->getDefaultValue();
			} catch (\ReflectionException) {
				$default = null;
			}

			$description = null;
			if ($comment !== null) {
				$pattern = '@(\S+)\s*(?:.*?)\$' . preg_quote($parameter->getName(), '/') . '\s+(.*?)';
				$paramAnnotation = Helpers::findCommentAnnotation($comment, 'param', $pattern);
				if ($paramAnnotation !== null) {
					$description = (string) preg_replace('/^' . $pattern . '$/', '$2', $paramAnnotation);
				}
			}
			assert($parameter->getName() !== '');

			$return[] = [
				'position' => $parameter->getPosition(),
				'name' => $parameter->getName(),
				'type' => $this->renderType($type, $enumValues),
				'default' => $default,
				'required' => $parameter->isOptional() === false,
				'description' => $description,
			];
		}

		return $return;
	}


	/**
	 * @return array<int, EntityPropertyMeta>
	 */
	private function processEntityProperties(string $entity): array
	{
		if (\class_exists($entity) === false) {
			throw new \InvalidArgumentException(sprintf('Entity "%s" is not valid class.', $entity));
		}

		$ref = new \ReflectionClass($entity);
		$entityInstance = $ref->newInstanceWithoutConstructor();
		$position = 0;
		$return = [];
		foreach ($ref->getProperties() as $property) {
			$property->setAccessible(true);
			assert($property->getName() !== '');
			[$description, $allowsNull, $scalarTypes, $entityClass] = $this->inspectPropertyInfo($property);
			$defaultValue = $this->resolvePropertyDefaultValue($property, $entityInstance, $entityClass);

			$return[] = new EntityPropertyMeta(
				position: $position++,
				name: $property->getName(),
				type: (static function () use ($entityClass, $scalarTypes, $allowsNull): string {
					$scalarTypes = array_merge($scalarTypes, $allowsNull ? ['null'] : []);

					return $entityClass ?? ($scalarTypes === [] ? 'mixed' : implode('|', $scalarTypes));
				})(),
				default: $defaultValue !== 'unknown' ? $defaultValue : '',
				required: ($entityClass !== null && $allowsNull === false)
					|| ($entityClass === null && ($defaultValue === null || $defaultValue === 'unknown')),
				description: $description,
				children: $entityClass !== null ? $this->processEntityProperties($entityClass) : [],
			);
		}

		return $return;
	}


	/**
	 * @return array{0: string|null, 1: bool, 2: array<int, string>, 3: class-string|null}
	 */
	private function inspectPropertyInfo(\ReflectionProperty $property): array
	{
		$comment = (string) $property->getDocComment();
		$propertyType = \Baraja\ServiceMethodInvoker\Helpers::resolvePropertyType($property);

		if ($propertyType !== null) {
			$requiredType = $propertyType;
			$propertyNativeType = $property->getType();
			if ($propertyNativeType !== null) {
				$requiredType .= $propertyNativeType->allowsNull() ? '|null' : '';
			}
		} elseif ($comment !== '' && preg_match('/@var\s+(\S+)/', $comment, $parser) === 1) { // scalar types only!
			$requiredType = $parser[1] !== '' ? $parser[1] : 'null';
		} else {
			$requiredType = 'null';
		}

		$allowsNull = false;
		$scalarTypes = [];
		$entityClass = null;

		foreach (explode('|', $requiredType) as $type) {
			if ($type === 'null') {
				$allowsNull = true;
			} elseif (isset(self::SCALAR_TYPES[$type]) === true) {
				$scalarTypes[] = $type;
			} elseif (\class_exists($type) === true) {
				$entityClass = $type;
			}
		}

		$description = null;
		if ($comment !== '') {
			$comment = (string) Helpers::findCommentDescription(Helpers::normalizeComment($comment));
			if ($comment !== '') {
				$description = $comment;
			}
		}

		return [$description, $allowsNull, $scalarTypes, $entityClass];
	}


	private function resolvePropertyDefaultValue(
		\ReflectionProperty $property,
		object $entityInstance,
		?string $entityClass,
	): ?string {
		$type = $property->getType();
		if ($entityClass !== null && $type !== null && $type->allowsNull() === false) {
			return '';
		}
		if ($property->isInitialized($entityInstance)) {
			$defaultValue = $property->getValue($entityInstance);
			if ($defaultValue === null) {
				return null;
			}
			if (is_bool($defaultValue)) {
				return $defaultValue ? 'true' : 'false';
			}
			if (is_scalar($defaultValue)) {
				return (string) $defaultValue;
			}

			return str_replace("\n", '', print_r($defaultValue, true));
		}

		return 'unknown';
	}


	/**
	 * @param array<int, string> $possibleValues
	 */
	private function renderType(?\ReflectionType $type, array $possibleValues = []): string
	{
		$renderType = $type === null ? '' : sprintf('%s%s', $type->getName(), $type->allowsNull() ? '|null' : '');
		$renderValues = $possibleValues !== [] ? sprintf('"%s"', implode('", "', $possibleValues)) : null;

		return trim($renderValues !== null ? sprintf('[%s] %s', $renderValues, $renderType) : ($renderType ?? '-'));
	}
}
