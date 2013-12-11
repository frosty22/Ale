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
		return $this->days * 86400 + $this->h * 3600 + $this->i * 60 + $this->s;
	}


}