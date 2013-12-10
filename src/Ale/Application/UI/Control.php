<?php

namespace Ale\Application\UI;

use Nette;

/**
 * Base control
 *
 * @copyright Copyright (c) 2013 Ledvinka Vít
 * @author Ledvinka Vít, frosty22 <ledvinka.vit@gmail.com>
 * @author Filip Procházka <filip@prochazka.su>
 *
 */
abstract class Control extends Nette\Application\UI\Control
{

	/**
	 */
	public function __construct()
	{
		parent::__construct();
	}


	/**
	 * @param string $class
	 * @return Nette\Templating\ITemplate
	 */
	protected function createTemplate($class = NULL)
	{
		/** @var \Nette\Templating\FileTemplate|\stdClass $template */
		$template = parent::createTemplate($class);
		$template->registerHelperLoader(callback($this->presenter->context->getByType('Ale\TemplateHelpers'), "loader"));

		if ($file = $this->getTemplateDefaultFile()) {
			$template->setFile($file);
		}
		return $template;
	}



	/**
	 * Derives template path from class name.
	 *
	 * @return null|string
	 */
	protected function getTemplateDefaultFile()
	{
		$refl = $this->getReflection();
		$file = dirname($refl->getFileName()) . '/' . lcfirst($refl->getShortName()) . '.latte';
		return file_exists($file) ? $file : NULL;
	}



}