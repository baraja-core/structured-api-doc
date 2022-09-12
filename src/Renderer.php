<?php

declare(strict_types=1);

namespace Baraja\StructuredApi\Doc;


use Baraja\StructuredApi\Doc\Descriptor\ApiAction;
use Baraja\StructuredApi\Doc\DTO\DocumentationResponse as DR;
use Baraja\StructuredApi\Doc\DTO\EndpointActionResponse;
use Baraja\StructuredApi\Doc\DTO\EndpointAggregatedParameterResponse;
use Baraja\StructuredApi\Doc\DTO\EndpointParameterResponse;
use Baraja\StructuredApi\Doc\DTO\EndpointPossibleResponseBadge;
use Baraja\StructuredApi\Doc\DTO\EntityPropertyMeta;
use Baraja\StructuredApi\Doc\DTO\EntityResponsePropertyResponse;
use Nette\Utils\Strings;

final class Renderer
{
	private const ScalarTypes = ['string' => 1, 'bool' => 1, 'int' => 1, 'float' => 1, 'array' => 1, 'null' => 1];


	/**
	 * @return array<int, DR>
	 */
	public function render(DocumentationInfo $documentation): array
	{
		$structure = [];
		foreach ($documentation->getEndpointsInfo() as $endpoint) {
			$route = $endpoint->getRoute();
			$comment = $endpoint->getComment();
			$name = $comment === null
				? null
				: Helpers::findCommentAnnotation($comment, 'endpointName');

			$structure[] = new DR(
				route: $route,
				class: $endpoint->getClass(),
				name: $name ?? Strings::firstUpper(str_replace('-', ' ', $route)),
				description: $comment === null ? null : Helpers::findCommentDescription($comment),
				public: $comment !== null && preg_match('/@public(?:$|\s|\n)/', $comment) === 1,
				actions: array_map(
					fn(ApiAction $action) => $this->processAction($action),
					$endpoint->getActionMethods(),
				),
			);
		}

		usort($structure, static fn(DR $a, DR $b): int => strcmp($a->route, $b->route));

		return $structure;
	}


	private function processAction(ApiAction $action): EndpointActionResponse
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

		$parameters = array_map(
			fn(\ReflectionParameter $parameter) => $this->processParameters($comment, $parameter),
			$action->getParameters(),
		);
		$parametersDeclaringType = null;
		if (
			count($parameters) === 1
			&& isset($parameters[0])
			&& $parameters[0] instanceof EndpointAggregatedParameterResponse
		) {
			$parametersDeclaringType = $parameters[0]->type;
			$parameters = $parameters[0]->objectProperties;
		}

		return new EndpointActionResponse(
			name: $action->getName(),
			method: $action->getMethod(),
			route: $action->getRoute(),
			httpMethod: $action->getHttpMethod(),
			methodName: $action->getMethodName(),
			description: $comment === null ? null : Helpers::findCommentDescription($comment),
			roles: $roles,
			throws: $throws,
			parameters: $parameters,
			parametersDeclaringType: $parametersDeclaringType,
			returnType: $action->getReturnType(),
			responses: $this->renderPossibleResponses($action),
		);
	}


	private function processParameters(?string $comment, \ReflectionParameter $parameter): EndpointParameterResponse
	{
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
				$enumValues = array_map(
					static fn(\UnitEnum $case): string => htmlspecialchars($case->value ?? $case->name),
					$typeName::cases(),
				);
			} else {
				$return = new EndpointAggregatedParameterResponse(
					position: 0,
					name: $parameter->getName(),
					type: $typeName,
					default: null,
					required: $parameter->isOptional() === false,
					description: null,
				);
				$return->objectProperties = array_map(
					static fn(EntityPropertyMeta $meta): EndpointParameterResponse => $meta->toEntity(),
					$this->processEntityProperties($typeName),
				);

				return $return;
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
		assert($parameter->getPosition() >= 0);

		return new EndpointParameterResponse(
			position: $parameter->getPosition(),
			name: $parameter->getName(),
			type: $this->renderType($type, $enumValues),
			default: $default,
			required: $parameter->isOptional() === false,
			description: $description,
		);
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
			[$description, $allowsNull, $scalarTypes, $entityClass] = $this->inspectPropertyInfo($property);
			$defaultValue = $this->resolvePropertyDefaultValue($property, $entityInstance, $entityClass);

			$return[] = new EntityPropertyMeta(
				position: $position++,
				name: $property->getName(),
				type: $this->serializeType($entityClass, $scalarTypes, $allowsNull),
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
			} elseif (isset(self::ScalarTypes[$type]) === true) {
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
	 * @return non-empty-string
	 */
	private function renderType(?\ReflectionType $type, array $possibleValues = []): string
	{
		$renderType = $type !== null
			? sprintf('%s%s', $type->getName(), $type->allowsNull() ? '|null' : '')
			: 'mixed';

		return $possibleValues !== []
			? sprintf('["%s"] %s', implode('", "', $possibleValues), $renderType)
			: $renderType;
	}


	/**
	 * @return array<int, EndpointPossibleResponseBadge>
	 */
	private function renderPossibleResponses(ApiAction $action): array
	{
		$returnType = $action->getReturnType();
		if (
			$returnType !== 'array'
			&& ($returnType === null || $returnType === 'void' || isset(self::ScalarTypes[$returnType]))
		) {
			return [];
		}

		$return = [];
		if (class_exists($returnType)) {
			$properties = $this->hydrateResponseStructure(new \ReflectionClass($returnType));
			$return[] = new EndpointPossibleResponseBadge(
				properties: $properties,
				typescriptDefinition: TypeScriptResponseHydration::hydrateDefinition($properties),
			);
		}

		return $return;
	}


	/**
	 * @return array<int, EntityResponsePropertyResponse>
	 */
	private function hydrateResponseStructure(\ReflectionClass $ref): array
	{
		$return = [];
		foreach ($ref->getProperties() as $property) {
			$property->setAccessible(true);
			[$description, $allowsNull, $scalarTypes, $entityClass] = $this->inspectPropertyInfo($property);
			$return[] = new EntityResponsePropertyResponse(
				name: $property->getName(),
				type: $this->serializeType($entityClass, $scalarTypes, $allowsNull),
				description: $description,
				annotation: $this->processPropertyAnnotation($property),
				nullable: $allowsNull,
				children: $entityClass !== null && class_exists($entityClass)
					? $this->hydrateResponseStructure(new \ReflectionClass($entityClass))
					: [],
			);
		}

		return $return;
	}


	/**
	 * @param class-string|null $entityClass
	 * @param array<int, string> $scalarTypes
	 * @return class-string|non-empty-string
	 */
	private function serializeType(?string $entityClass, array $scalarTypes, bool $allowsNull): string
	{
		$scalarTypes = array_merge($scalarTypes, $allowsNull === true ? ['null'] : []);

		return $entityClass ?? ($scalarTypes === [] ? 'mixed' : implode('|', $scalarTypes));
	}


	private function processPropertyAnnotation(\ReflectionProperty $property): ?string
	{
		$name = $property->getName();
		$entity = $property->getDeclaringClass();
		$entityConstructor = $entity->getConstructor();
		$entityDoc = trim((string) ($entityConstructor !== null ? $entityConstructor->getDocComment() : ''));
		if ($entityDoc === '') {
			return null;
		}

		preg_match_all('/@param\s+([^\$]+?)\s+\$(\w+)\n/', $entityDoc, $matches);
		for ($i = 0; isset($matches[2][$i]); $i++) {
			if (($matches[2][$i] ?? '') === $name) {
				$return = (string) ($matches[1][$i] ?? '');
				$return = (string) preg_replace('/(?:\s|\n)+\*(?:\s|\n)+/', ' ', $return);
				$return = (string) preg_replace('/\s+/', ' ', $return);

				return trim($return);
			}
		}

		return null;
	}
}
