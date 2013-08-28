<?php

namespace Ale\Application\UI;

use Nette\Reflection\Method;
use Nette\Reflection\Property;
use Nette\Reflection\ClassType;
use Nette\Utils\Strings;

/**
 * Base for presenters.
 *
 * Contain (used instead of TRAIT for support PHP5.3):
 *
 * @link http://forum.nette.org/cs/13568-router-vracia-objekty-entity-namiesto-skalarov#p102228
 * @link https://github.com/Kdyby/Autowired/blob/master/src/Kdyby/Autowired/AutowireProperties.php
 * @link https://github.com/Kdyby/Autowired/blob/master/src/Kdyby/Autowired/AutowireComponentFactories.php
 *
 * @copyright Copyright (c) 2013 Ledvinka Vít
 * @author Ledvinka Vít, frosty22 <ledvinka.vit@gmail.com>
 *
 */
abstract class Presenter extends \Nette\Application\UI\Presenter {



	/**
	 * Redirect to URL with POST params.
	 * @param string $url
	 * @param array $getParams
	 * @param array $postParams
	 * @param null|int $code
	 */
	protected function redirectUrlPost($url, array $getParams = array(), array $postParams = array(), $code = NULL)
	{
		if (!$code) {
			$code = \Nette\Http\IResponse::S303_POST_GET;
		}
		$this->sendResponse(new \Ale\Application\Responses\RedirectPostResponse($url, $getParams, $postParams, $code));
	}



	/*******************************************************************************************************************/
	/**
	 * Extended for support of action*, render* and handle* methods with objects
	 * from repositories by primary key.
	 * @link http://forum.nette.org/cs/13568-router-vracia-objekty-entity-namiesto-skalarov#p102228
	 */


	/**
	 * @autowire
	 * @var \Kdyby\Doctrine\EntityManager
	 */
	public $entityManager;


	/**
	 * Calls public method if exists.
	 * @param string $method
	 * @param array $params
	 * @throws \Nette\Application\BadRequestException
	 * @return bool  does method exist?
	 */
	protected function tryCall($method, array $params)
	{
		$rc = $this->getReflection();
		if ($rc->hasMethod($method)) {
			$rm = $rc->getMethod($method);
			if ($rm->isPublic() && !$rm->isAbstract() && !$rm->isStatic()) {
				$this->checkRequirements($rm);
				$args = $rc->combineArgs($rm, $params);

				if (\Nette\Utils\Strings::match($method, "~^(action|render|handle).+~")) {

					$methodParams = $rm->getParameters();
					foreach ($methodParams as $i => $param) {
						/** @var \Nette\Reflection\Parameter $param */
						if ($className = $param->getClassName()) {
							if ($paramValue = $args[$i]) {
								$entity = $this->findById($className, $paramValue);
								if ($entity) {
									$args[$i] = $entity;
								} else {
									throw new \Nette\Application\BadRequestException("Value '$paramValue' not found in collection '$className'.");
								}
							} else {
								if (!$param->allowsNull()) {
									throw new \Nette\Application\BadRequestException("Value '$param' cannot be NULL.");
								}
							}

						}
					}

				}

				$rm->invokeArgs($this, $args);
				return TRUE;
			}
		}

		return FALSE;
	}


	/**
	 * Find entity by ID.
	 * @param string $entityName
	 * @param int $id
	 * @return object
	 */
	protected function findById($entityName, $id)
	{
		return $this->entityManager->find($entityName, $id);
	}



	/*******************************************************************************************************************/
	/**
	 * Autowire properties.
	 * Included instead of traits for support < PHP 5.4
	 * @link https://github.com/Kdyby/Autowired/blob/master/src/Kdyby/Autowired/AutowireProperties.php
	 */


	/**
	 * @var array
	 */
	private $autowireProperties = array();

	/**
	 * @var \Nette\DI\Container
	 */
	private $autowirePropertiesLocator;



	/**
	 * @param \Nette\DI\Container $dic
	 * @throws \Kdyby\Autowired\MemberAccessException
	 * @throws \Kdyby\Autowired\MissingServiceException
	 * @throws \Kdyby\Autowired\InvalidStateException
	 * @throws \Kdyby\Autowired\UnexpectedValueException
	 */
	public function injectProperties(\Nette\DI\Container $dic)
	{
		if (!$this instanceof \Nette\Application\UI\PresenterComponent) {
			throw new \Kdyby\Autowired\MemberAccessException('Trait ' . __TRAIT__ . ' can be used only in descendants of PresenterComponent.');
		}

		$this->autowirePropertiesLocator = $dic;
		$cache = new \Nette\Caching\Cache($dic->getByType('Nette\Caching\IStorage'), 'Kdyby.Autowired.AutowireProperties');

		if (is_array($this->autowireProperties = $cache->load($presenterClass = get_class($this)))) {
			foreach ($this->autowireProperties as $propName => $tmp) {
				unset($this->{$propName});
			}

			return;
		}

		$this->autowireProperties = array();

		$ignore = class_parents('Nette\Application\UI\Presenter') + array('ui' => 'Nette\Application\UI\Presenter');
		foreach ($this->getReflection()->getProperties() as $prop) {
			/** @var Property $prop */
			if (!$this->validateProperty($prop, $ignore)) {
				continue;
			}

			$this->resolveProperty($prop);
		}

		$files = array_map(function ($class) {
			return ClassType::from($class)->getFileName();
		}, array_diff(array_values(class_parents($presenterClass) + array('me' => $presenterClass)), $ignore));

		$files[] = ClassType::from($this->autowirePropertiesLocator)->getFileName();

		$cache->save($presenterClass, $this->autowireProperties, array(
			$cache::FILES => $files,
		));
	}



	private function validateProperty(Property $property, array $ignore)
	{
		if (in_array($property->getDeclaringClass()->getName(), $ignore)) {
			return FALSE;
		}

		foreach ($property->getAnnotations() as $name => $value) {
			if (!in_array(Strings::lower($name), array('autowire', 'autowired'), TRUE)) {
				continue;
			}

			if (Strings::lower($name) !== $name || $name !== 'autowire') {
				throw new \Kdyby\Autowired\UnexpectedValueException("Annotation @$name on $property should be fixed to lowercase @autowire.");
			}

			if ($property->isPrivate()) {
				throw new \Kdyby\Autowired\MemberAccessException("Autowired properties must be protected or public. Please fix visibility of $property or remove the @autowire annotation.");
			}

			return TRUE;
		}

		return FALSE;
	}



	/**
	 * @param string $type
	 * @return string|bool
	 */
	private function findByTypeForProperty($type)
	{
		if (method_exists($this->autowirePropertiesLocator, 'findByType')) {
			$found = $this->autowirePropertiesLocator->findByType($type);

			return reset($found);
		}

		$type = ltrim(strtolower($type), '\\');

		return !empty($this->autowirePropertiesLocator->classes[$type])
			? $this->autowirePropertiesLocator->classes[$type]
			: FALSE;
	}



	/**
	 * @param Property $prop
	 * @throws \Kdyby\Autowired\MissingServiceException
	 * @throws \Kdyby\Autowired\UnexpectedValueException
	 */
	private function resolveProperty(Property $prop)
	{
		$type = $this->resolveAnnotationClass($prop, $prop->getAnnotation('var'), 'var');
		$metadata = array(
			'value' => NULL,
			'type' => $type,
		);

		if (($args = (array) $prop->getAnnotation('autowire')) && !empty($args['factory'])) {
			$factoryType = $this->resolveAnnotationClass($prop, $args['factory'], 'autowire');

			if (!$this->findByTypeForProperty($factoryType)) {
				throw new \Kdyby\Autowired\MissingServiceException("Factory of type \"$factoryType\" not found for $prop in annotation @autowire.");
			}

			$factoryMethod = Method::from($factoryType, 'create');
			$createsType = $this->resolveAnnotationClass($factoryMethod, $factoryMethod->getAnnotation('return'), 'return');
			if ($createsType !== $type) {
				throw new \Kdyby\Autowired\UnexpectedValueException("The property $prop requires $type, but factory of type $factoryType, that creates $createsType was provided.");
			}

			unset($args['factory']);
			$metadata['arguments'] = array_values($args);
			$metadata['factory'] = $this->findByTypeForProperty($factoryType);

		} elseif (!$this->findByTypeForProperty($type)) {
			throw new \Kdyby\Autowired\MissingServiceException("Service of type \"$type\" not found for $prop in annotation @var.");
		}

		// unset property to pass control to __set() and __get()
		unset($this->{$prop->getName()});
		$this->autowireProperties[$prop->getName()] = $metadata;
	}



	private function resolveAnnotationClass(\Reflector $prop, $annotationValue, $annotationName)
	{
		/** @var Property|Method $prop */

		if (!$type = ltrim($annotationValue, '\\')) {
			throw new \Kdyby\Autowired\InvalidStateException("Missing annotation @{$annotationName} with typehint on {$prop}.");
		}

		if (!class_exists($type) && !interface_exists($type)) {
			if (substr(func_get_arg(1), 0, 1) === '\\') {
				throw new \Kdyby\Autowired\MissingClassException("Class \"$type\" was not found, please check the typehint on {$prop} in annotation @{$annotationName}.");
			}

			if (!class_exists($type = $prop->getDeclaringClass()->getNamespaceName() . '\\' . $type) && !interface_exists($type)) {
				throw new \Kdyby\Autowired\MissingClassException("Neither class \"" . func_get_arg(1) . "\" or \"{$type}\" was found, please check the typehint on {$prop} in annotation @{$annotationName}.");
			}
		}

		return ClassType::from($type)->getName();
	}



	/**
	 * @param string $name
	 * @param mixed $value
	 * @throws \Kdyby\Autowired\MemberAccessException
	 * @return mixed
	 */
	public function __set($name, $value)
	{
		if (!isset($this->autowireProperties[$name])) {
			return parent::__set($name, $value);

		} elseif ($this->autowireProperties[$name]['value']) {
			throw new \Kdyby\Autowired\MemberAccessException("Property \$$name has already been set.");

		} elseif (!$value instanceof $this->autowireProperties[$name]['type']) {
			throw new \Kdyby\Autowired\MemberAccessException("Property \$$name must be an instance of " . $this->autowireProperties[$name]['type'] . ".");
		}

		return $this->autowireProperties[$name]['value'] = $value;
	}



	/**
	 * @param $name
	 * @throws \Kdyby\Autowired\MemberAccessException
	 * @return mixed
	 */
	public function &__get($name)
	{
		if (!isset($this->autowireProperties[$name])) {
			return parent::__get($name);
		}

		if (empty($this->autowireProperties[$name]['value'])) {
			if (!empty($this->autowireProperties[$name]['factory'])) {
				$factory = callback($this->autowirePropertiesLocator->getService($this->autowireProperties[$name]['factory']), 'create');
				$this->autowireProperties[$name]['value'] = $factory->invokeArgs($this->autowireProperties[$name]['arguments']);

			} else {
				$this->autowireProperties[$name]['value'] = $this->autowirePropertiesLocator->getByType($this->autowireProperties[$name]['type']);
			}
		}

		return $this->autowireProperties[$name]['value'];
	}



	/*******************************************************************************************************************/
	/**
	 * Autowire component factories.
	 * Included instead of traits for support < PHP 5.4
	 * @link https://github.com/Kdyby/Autowired/blob/master/src/Kdyby/Autowired/AutowireComponentFactories.php
	 */


	/**
	 * @var \Nette\DI\Container
	 */
	private $autowireComponentFactoriesLocator;



	/**
	 * @return \Nette\DI\Container
	 */
	protected function getComponentFactoriesLocator()
	{
		if ($this->autowireComponentFactoriesLocator === NULL) {
			$this->injectComponentFactories($this->getPresenter()->getContext());
		}

		return $this->autowireComponentFactoriesLocator;
	}


	/**
	 * @param \Nette\DI\Container $dic
	 * @throws \Kdyby\Autowired\MemberAccessException
	 * @throws \Kdyby\Autowired\MissingServiceException
	 * @internal
	 */
	public function injectComponentFactories(\Nette\DI\Container $dic)
	{
		if (!$this instanceof \Nette\Application\UI\PresenterComponent) {
			throw new \Kdyby\Autowired\MemberAccessException('Trait ' . __TRAIT__ . ' can be used only in descendants of PresenterComponent.');
		}

		$this->autowireComponentFactoriesLocator = $dic;
		$cache = new \Nette\Caching\Cache($dic->getByType('Nette\Caching\IStorage'), 'Kdyby.Autowired.AutowireComponentFactories');

		if ($cache->load($presenterClass = get_class($this)) !== NULL) {
			return;
		}

		$rc = $this->getReflection();
		$ignore = class_parents('Nette\Application\UI\Presenter') + array('ui' => 'Nette\Application\UI\Presenter');
		foreach ($rc->getMethods() as $method) {
			/** @var Property $prop */
			if (in_array($method->getDeclaringClass()->getName(), $ignore) || !Strings::startsWith($method->getName(), 'createComponent')) {
				continue;
			}

			foreach ($method->getParameters() as $parameter) {
				if (!$class = $parameter->getClassName()) { // has object type hint
					continue;
				}

				if (!$this->findByTypeForFactory($class) && !$parameter->allowsNull()) {
					throw new \Kdyby\Autowired\MissingServiceException("No service of type {$class} found. Make sure the type hint in $method is written correctly and service of this type is registered.");
				}
			}
		}

		$files = array_map(function ($class) {
			return ClassType::from($class)->getFileName();
		}, array_diff(array_values(class_parents($presenterClass) + array('me' => $presenterClass)), $ignore));

		$files[] = ClassType::from($this->autowireComponentFactoriesLocator)->getFileName();

		$cache->save($presenterClass, TRUE, array(
			$cache::FILES => $files,
		));
	}



	/**
	 * @param string $type
	 * @return string|bool
	 */
	private function findByTypeForFactory($type)
	{
		if (method_exists($this->autowireComponentFactoriesLocator, 'findByType')) {
			$found = $this->autowireComponentFactoriesLocator->findByType($type);

			return reset($found);
		}

		$type = ltrim(strtolower($type), '\\');

		return !empty($this->autowireComponentFactoriesLocator->classes[$type])
			? $this->autowireComponentFactoriesLocator->classes[$type]
			: FALSE;
	}



	/**
	 * @param $name
	 * @return \Nette\ComponentModel\IComponent
	 * @throws \Nette\UnexpectedValueException
	 */
	public function createComponent($name)
	{
		$sl = $this->getComponentFactoriesLocator();

		$ucName = ucfirst($name);
		$method = 'createComponent' . $ucName;
		if ($ucName !== $name && method_exists($this, $method)) {
			$reflection = $this->getReflection()->getMethod($method);
			if ($reflection->getName() !== $method) {
				return;
			}
			$parameters = $reflection->parameters;

			$args = array();
			if (($first = reset($parameters)) && !$first->className) {
				$args[] = $name;
			}

			$args = \Nette\DI\Helpers::autowireArguments($reflection, $args, $sl);
			$component = call_user_func_array(array($this, $method), $args);
			if (!$component instanceof \Nette\ComponentModel\IComponent && !isset($this->components[$name])) {
				throw new \Nette\UnexpectedValueException("Method $reflection did not return or create the desired component.");
			}

			return $component;
		}
	}


}