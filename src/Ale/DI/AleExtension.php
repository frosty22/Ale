<?php

namespace Ale\DI;

use Nette\Config\CompilerExtension;
use Nette\DI\ServiceDefinition;
use Nette\Utils\Validators;

/**
 * Ale extension for modular support.
 *
 * @copyright Copyright (c) 2013 Ledvinka Vít
 * @author Ledvinka Vít, frosty22 <ledvinka.vit@gmail.com>
 *
 */
class AleExtension extends CompilerExtension
{


	/**
	 * @var array
	 */
	private $defaults = array(
		'helpers' => array()
	);


	/**
	 * Base configuration
	 */
	public function loadConfiguration()
	{
		$config = $this->getConfig($this->defaults);
		$builder = $this->getContainerBuilder();

		if (isset($this->config["nette"]["application"]["mapping"])) {
			$builder->getDefinition("nette.presenterFactory")
				->addSetup('setMapping', array($this->config["nette"]["application"]["mapping"]));
		}

		$templateHelpers = $builder->addDefinition($this->prefix('templateHelpers'))
			->setClass('Ale\TemplateHelpers');

		foreach ($config['helpers'] as $name => $helper)
			$templateHelpers->addSetup('addHelper', array($name, $helper));

		$builder->addDefinition($this->prefix('daoFactory'))
			->setClass('Ale\DaoFactory');
	}


	/**
	 * Call before container compile
	 */
	public function beforeCompile()
	{
		// Autowire for @doctrine.dao depends on annotations
		foreach($this->getContainerBuilder()->getDefinitions() as $definition) {
			$this->extendAutowire($definition);
		}
	}


	/**
	 * Browse methods for autowire
	 * @param ServiceDefinition $definition
	 */
	protected function extendAutowire(ServiceDefinition $definition)
	{
		if (!$definition->autowired)
			return;

		if ($definition->class)
			$reflection = new \Nette\Reflection\ClassType($definition->class);
		elseif ($definition->factory) {
			try {
				$reflection = new \Nette\Reflection\ClassType($definition->factory->entity);
			} catch (\ReflectionException $e) {
				return;
			}
		} else
			return;

		if ($method = $reflection->getConstructor()) {
			$this->autowireParams($definition, $method);
		}

		return;
	}


	/**
	 * Create definition statement for method
	 * @param ServiceDefinition $definition
	 * @param \Nette\Reflection\Method $method
	 */
	protected function autowireParams(ServiceDefinition $definition, \Nette\Reflection\Method $method)
	{
		$parameters = $method->getParameters();
		foreach ($parameters as $num => $param) {
			/** @var \Nette\Reflection\Parameter $param */
			if ($targetClass = $param->getClass()) {
				if ($targetClass->getName() === 'Kdyby\Doctrine\EntityDao' && !isset($definition->factory->arguments[$num])) {
					$annotations = $method->getAnnotations();
					$entity = $this->getEntityName($param, $annotations);

					if ($definition->factory === NULL) {
						$definition->setFactory($definition->class);
					}

					$definition->factory->arguments[$num] = new \Nette\DI\Statement('@doctrine.dao', array($entity));
				}
			}
		}
	}


	/**
	 * Get required entity name from annotation
	 * @param \Nette\Reflection\Parameter $param
	 * @param array $annotations
	 * @return string
	 * @throws \Ale\ExceptedAnnotationException
	 */
	protected function getEntityName(\Nette\Reflection\Parameter $param, array $annotations)
	{
		$class = $param->declaringClass;

		if (!isset($annotations["param"]))
			throw new \Ale\ExceptedAnnotationException("Annotation @param is excepted in class
						 " . $class->name . " for support of autowire EntityDao.");

		$entity = NULL;
		foreach ($annotations["param"] as $annotation) {
			if ($match = \Nette\Utils\Strings::match($annotation, '~\\$' . $param->name . ' ([A-Z0-9\\\]+)~i')) {
				$entity = $match[1];
			}
		}

		if (!$entity)
			throw new \Ale\ExceptedAnnotationException('Excepted annotation "@param EntityDao
									$' . $param->name . ' EntityName" in ' . $class->name . ' but not found.');

		if (!$this->checkEntity($entity))
			throw new \Ale\ExceptedAnnotationException('Defined entity in ' . $class->name . ' at param $' . $param->name . ' named
									"' . $entity . '" doesnt exists.');

		return $entity;
	}


	/**
	 * Check if entity exist
	 * @param string $entity
	 * @return bool
	 */
	protected function checkEntity($entity)
	{
		return class_exists($entity);
	}

}
