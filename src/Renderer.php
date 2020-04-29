<?php

declare(strict_types=1);

namespace Baraja\StructuredApi\Doc;


use Baraja\StructuredApi\Doc\Descriptor\ApiAction;
use Latte\Engine;
use Nette\Utils\Strings;

final class Renderer
{

	/**
	 * @param DocumentationInfo $documentation
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

			$structure[$route] = [
				'route' => $route,
				'class' => $endpoint->getClass(),
				'name' => $name ?: Strings::firstUpper(str_replace('-', ' ', $route)),
				'description' => $comment === null ? null : Helpers::findCommentDescription($comment),
				'actions' => $actions,
			];
		}

		(new Engine)->render(__DIR__ . '/basic.latte', [
			'documentation' => $documentation,
			'errors' => $errors,
			'structure' => $structure,
		]);
	}


	/**
	 * @param ApiAction $action
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
			'throws' => $comment === null ? [] : Helpers::findAllCommentAnnotations($comment, 'throws'),
			'parameters' => $this->processParameters($comment, $action->getParameters()),
		];
	}


	/**
	 * @param string|null $comment
	 * @param \ReflectionParameter[] $parameters
	 * @return mixed[]
	 */
	private function processParameters(?string $comment, array $parameters): array
	{
		$return = [];

		foreach ($parameters as $parameter) {
			$type = $parameter->getType();

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
}