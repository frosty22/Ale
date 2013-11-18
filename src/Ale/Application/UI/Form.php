<?php

namespace Ale\Application\UI;

use Ale\InvalidCallException;
use Kdyby\BootstrapFormRenderer\BootstrapRenderer;

/**
 *
 * @copyright Copyright (c) 2013 Ledvinka Vít
 * @author Ledvinka Vít, frosty22 <ledvinka.vit@gmail.com>
 *
 */
class Form extends \Nette\Application\UI\Form {


	/**
	 * @var bool
	 */
	private $monitored = FALSE;


	/**
	 * Monitor it.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->monitor('Nette\Application\UI\Control');
		$this->monitored = TRUE;
	}


	/**
	 * @throws InvalidCallException
	 */
	public function render()
	{
		if (!$this->monitored) {
			throw new InvalidCallException("Contructor of this form must be call, for monitor control.");
		}

		// Can't call parent::render() because it make: too much open files error
		$args = func_get_args();
		array_unshift($args, $this);
		echo call_user_func_array(array($this->getRenderer(), 'render'), $args);
	}


	/**
	 * @param \Nette\ComponentModel\Container $parent
	 */
	protected function attached($parent)
	{
		parent::attached($parent);

		if ($parent instanceof Control) {
			$this->setRenderer(new BootstrapRenderer(clone $parent->template));
		}
	}


	/**
	 * @param string $message
	 * @param array $args
	 */
	public function addError($message, $args = array())
	{
		// Hack for translator - and key string like "foo.bar"
		if ($this->translator && \Nette\Utils\Strings::match($message, '~^[a-z\.]+$~i')) {
			$message = $this->translator->translate($message, $args);
		}
		parent::addError($message);
	}



}
