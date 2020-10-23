<?php

declare(strict_types=1);

namespace Baraja\StructuredApi\Doc;


use Baraja\StructuredApi\Doc\Descriptor\ApiAction;
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
			$name = $comment === null ? null : Helpers::findCommentAnnotation($comment, 'endpointName');

			$actions = [];
			foreach ($endpoint->getActionMethods() as $action) {
				$actions[] = $this->processAction($action);
			}

			$structure[] = [
				'route' => $route,
				'class' => $endpoint->getClass(),
				'name' => $name ?: Strings::firstUpper(str_replace('-', ' ', $route)),
				'description' => $comment === null ? null : Helpers::findCommentDescription($comment),
				'public' => $comment !== null && (bool) preg_match('/@public(?:$|\s|\n)/', (string) $comment),
				'actions' => $actions,
			];
		}

		usort($structure, static function (array $a, array $b): int {
			return strcmp($a['route'], $b['route']);
		});

		(new Engine)->render(__DIR__ . '/basic.latte', [
			'documentation' => $documentation,
			'errors' => $errors,
			'structure' => $structure,
		]);
	}


	/**
	 * @return mixed[]
	 */
	private function processAction(ApiAction $action): array
	{
		return [
			'name' => $action->getName(),
			'method' => $action->getMethod(),
			'route' => $action->getRoute(),
			'httpMethod' => $action->getHttpMethod(),
			'methodName' => $action->getMethodName(),
			'description' => ($comment = $action->getComment()) === null ? null : Helpers::findCommentDescription($comment),
			'roles' => $comment !== null ? \Baraja\StructuredApi\Helpers::parseRolesFromComment($comment) : [],
			'throws' => $comment === null ? [] : Helpers::findAllCommentAnnotations($comment, 'throws'),
			'parameters' => $this->processParameters($comment, $action->getParameters()),
		];
	}


	/**
	 * @param \ReflectionParameter[] $parameters
	 * @return mixed[]
	 */
	private function processParameters(?string $comment, array $parameters): array
	{
		$return = [];

		foreach ($parameters as $parameter) {
			$type = $parameter->getType();
			$typeName = $type === null ? null : $type->getName();
			if ($typeName !== null && $typeName !== 'string' && $typeName !== 'int' && \class_exists($typeName) === true) {
				return $this->processEntityProperties($typeName);
			}
			try {
				$default = $parameter->getDefaultValue();
			} catch (\ReflectionException $e) {
				$default = null;
			}

			$description = null;
			if ($comment !== null) {
				$pattern = '@(\S+)\s*(?:.*?)\$' . preg_quote($parameter->getName(), '/') . '\s+(.*?)';
				if (($paramAnnotation = Helpers::findCommentAnnotation($comment, 'param', $pattern)) !== null) {
					$description = preg_replace('/^' . $pattern . '$/', '$2', $paramAnnotation);
				}
			}

			$return[] = [
				'position' => $parameter->getPosition(),
				'name' => $parameter->getName(),
				'type' => $type === null ? '-' : $type->getName() . ($type->allowsNull() ? '|null' : ''),
				'default' => $default,
				'required' => $parameter->isOptional() === false,
				'description' => $description,
			];
		}

		return $return;
	}


	/**
	 * @return mixed[]
	 */
	private function processEntityProperties(string $entity): array
	{
		try {
			$ref = new \ReflectionClass($entity);
		} catch (\ReflectionException $e) {
			return [];
		}

		$entityInstance = $ref->newInstanceWithoutConstructor();
		$position = 0;
		$return = [];
		foreach ($ref->getProperties() as $property) {
			[$description, $allowsNull, $scalarTypes, $entityClass] = $this->inspectPropertyComment($property->getDocComment() ?? '');
			$return[] = [
				'position' => $position++,
				'name' => $property->getName(),
				'type' => $entityClass ?? implode('|', array_merge($scalarTypes, $allowsNull ? ['null'] : [])),
				'default' => $defaultValue = $property->getValue($entityInstance),
				'required' => $entityClass === null && $allowsNull === false && $defaultValue === null,
				'description' => $description,
				'children' => $entityClass !== null ? $this->processEntityProperties((string) $entityClass) : null,
			];
		}

		return $return;
	}


	/**
	 * @return mixed[]
	 */
	private function inspectPropertyComment(string $comment): array
	{
		if (preg_match('/\@var\s+(\S+)/', $comment, $parser)) {
			$requiredType = $parser[1] ?: 'null';
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

		if ($comment !== '') {
			$description = Helpers::findCommentDescription(Helpers::normalizeComment($comment)) ?: null;
		} else {
			$description = null;
		}

		return [$description, $allowsNull, $scalarTypes, $entityClass];
	}
}
