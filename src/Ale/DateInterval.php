<?php

namespace Ale;

/**
 *
 * @copyright Copyright (c) 2013 Ledvinka Vít
 * @author Ledvinka Vít, frosty22 <ledvinka.vit@gmail.com>
 *
 */
class DateInterval extends \DateInterval {


	/**
	 * Get total number of seconds
	 * @return int
	 */
	public function getTotalSeconds()
	{
		return ($this->s)
		+ ($this->i * 60)
		+ ($this->h * 60 * 60)
		+ ($this->d * 60 * 60 * 24)
		+ ($this->m * 60 * 60 * 24 * 30) 	// Month - usually days count 30
		+ ($this->y * 60 * 60 * 24 * 365);	// Year - usually days count 365
	}


}