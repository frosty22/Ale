<?php

namespace Ale;

/**
 * Template helper loader.
 *
 * @copyright: Copyright (c) 2013 Ledvinka Vít
 * @author: Ledvinka Vít, frosty22 <ledvinka.vit@gmail.com>
 *
 */
class TemplateHelpers extends \Nette\Object
{

	/**
	 * Array of object helpers.
	 * @var array
	 */
	private $helpers = array();


	/**
	 * Add helper.
	 * @param string $name Name of helper
	 * @param object|callbakc $service
	 */
	public function addHelper($name, $service)
	{
		if (!is_callable($service)) {

			if (!is_object($service))
				throw new InvalidArgumentException('Property must be service or callback,
							but "' . gettype($service) . '" given.');

			if (!method_exists($service, 'helper'))
				throw new InvalidArgumentException('Service ' . gettype($service) . ' doesnt have method "helper".');

			$callback = array($service, 'helper');
		} else {
			$callback = $service;
		}

		$name = strtolower($name);
		$this->helpers[$name] = $callback;
	}


	/**
	 * Loader
	 * @param string $name Name of helper
	 * @return \Nette\Callback|null
	 */
	public function loader($name)
	{
		$name = strtolower($name);
		return isset($this->helpers[$name]) ? $this->helpers[$name] : NULL;
	}




}
