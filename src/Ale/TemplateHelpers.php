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
	 * @param callback $callback
	 */
	public function addHelper($name, \Nette\Callback $callback)
	{
		if (!$callback->isCallable())
			throw new \Nette\InvalidArgumentException("Callback of helper '$name' is not callable.");
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
