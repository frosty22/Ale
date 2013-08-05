<?php

namespace Ale\DI;

/**
 *
 *
 * @copyright Copyright (c) 2013 Ledvinka Vít
 * @author Ledvinka Vít, frosty22 <ledvinka.vit@gmail.com>
 *
 */
interface IPresenterProvider {


	/**
	 * Returns array of ClassNameMask => PresenterNameMask
	 * @see https://github.com/nette/nette/blob/master/Nette/Application/PresenterFactory.php#L138
	 */
	public function getPresenterMappings();

}