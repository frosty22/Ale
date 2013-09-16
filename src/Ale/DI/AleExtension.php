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
	 * Base configuration
	 */
	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		if (isset($this->config["nette"]["application"]["mapping"])) {
			$builder->getDefinition("nette.presenterFactory")
				->addSetup('setMapping', array($this->config["nette"]["application"]["mapping"]));
		}

		$builder->addDefinition($this->prefix('templateHelpers'))
			->setClass('Ale\TemplateHelpers');
	}



}