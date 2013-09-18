<?php

namespace Ale\DI;

use Nette\Config\CompilerExtension;
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



}
