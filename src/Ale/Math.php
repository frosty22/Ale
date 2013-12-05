<?php

namespace Ale;

/**
 *
 * Simple usually math operations
 *
 * @copyright Copyright (c) 2013 Ledvinka Vít
 * @author Ledvinka Vít, frosty22 <ledvinka.vit@gmail.com>
 *
 */
class Math {


	/**
	 * Get percent from part
	 * @param $amount Amount
	 * @param int $all Total amount
	 * @param int $precision
	 * @return float
	 */
	public static function getPercentFromPart($amount, $all, $precision = 0)
	{
		if ($all === 0) return 0;
		return round($amount / self::getOnePercent($all), $precision);
	}


	/**
	 * Get one percent
	 * @param int $amount Total amount
	 * @param int $precision Precision
	 * @return float
	 */
	public static function getOnePercent($amount, $precision = 4)
	{
		return round($amount / 100, $precision);
	}


	/**
	 * Sum all args
	 * @return int
	 */
	public static function sum()
	{
		$sum = 0;
		foreach (func_get_args() as $value) $sum += $value;
		return $sum;
	}


}