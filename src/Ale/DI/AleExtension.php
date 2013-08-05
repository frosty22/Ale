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

		// Changed default nette presenter factory
		$builder->removeDefinition("nette.presenterFactory");
		$builder->addDefinition("nette.presenterFactory")
			->setClass('Ale\Application\PresenterFactory', array('%appDir%'))
			->setAutowired(TRUE)
			->setShared(TRUE);

	}


	/**
	 * Call before container compile
	 */
	public function beforeCompile()
	{
		// Get mapping for presenters from extensions
		$presenterFactory = $this->containerBuilder->getDefinition("nette.presenterFactory");
		foreach ($this->compiler->getExtensions() as $extension) {
			if ($extension instanceof IPresenterProvider) {
				$mapping = $extension->getPresenterMappings();
				Validators::assert($mapping, 'array:1..');
				$presenterFactory->addSetup("setMapping", array($mapping));
			}
		}



	}


}