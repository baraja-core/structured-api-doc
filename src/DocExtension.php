<?php

declare(strict_types=1);

namespace Baraja\StructuredApi\Doc;


use Nette\Application\Application;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\PhpGenerator\ClassType;
use Nette\Security\User;

final class DocExtension extends CompilerExtension
{
	public function beforeCompile(): void
	{
		$documentation = $this->getContainerBuilder()->addDefinition($this->prefix('documentation'))
			->setFactory(Documentation::class);

		if (\class_exists('Nette\Security\User') === true) {
			$documentation->addSetup('?->injectUser($this->getByType(?))', ['@self', User::class]);
		}
	}


	public function afterCompile(ClassType $class): void
	{
		if (PHP_SAPI === 'cli') {
			return;
		}
		/** @var ServiceDefinition $application */
		$application = $this->getContainerBuilder()->getDefinitionByType(Application::class);

		/** @var ServiceDefinition $documentation */
		$documentation = $this->getContainerBuilder()->getDefinitionByType(Documentation::class);

		$class->getMethod('initialize')->addBody(
			'// Structured API documentation.' . "\n"
			. '(function () {' . "\n"
			. "\t" . 'if (strncmp(' . Helpers::class . '::processPath($this->getService(\'http.request\')), \'api-documentation\', 17) === 0) {' . "\n"
			. "\t\t" . '$this->getService(?)->onStartup[] = function(' . Application::class . ' $a): void {' . "\n"
			. "\t\t\t" . '$this->getService(\'' . $documentation->getName() . '\')->run();' . "\n"
			. "\t\t" . '};' . "\n"
			. "\t" . '}' . "\n"
			. '})();' . "\n",
			[$application->getName()],
		);
	}
}
