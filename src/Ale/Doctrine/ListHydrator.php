<?php

namespace Ale\Doctrine;

use Doctrine\ORM\Internal\Hydration\AbstractHydrator;
use PDO;

/**
 *
 * Hydrate to the associative array
 *
 * @copyright Copyright (c) 2013 Ledvinka Vít
 * @author Ledvinka Vít, frosty22 <ledvinka.vit@gmail.com>
 *
 */
class ListHydrator extends AbstractHydrator {


	/**
	 * @return array
	 */
	protected function hydrateAllData()
	{
		$result = array();
		$cache  = array();
		foreach($this->_stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
			$this->hydrateRowData($row, $cache, $result);
		}
		return $result;
	}


	/**
	 * @param array $row
	 * @param array $cache
	 * @param array $result
	 * @return bool|void
	 */
	protected function hydrateRowData(array $row, array &$cache, array &$result)
	{
		if(count($row) == 0) {
			return false;
		}

		$keys = array_keys($row);

		// Assume first column is id field
		$id = $row[$keys[0]];


		if(count($row) == 2) {
			// If only one more field assume that this is the value field
			$value = $row[$keys[1]];
		} else {
			// Remove ID field and add remaining fields as value array
			array_shift($row);
			$value = $row;
		}

		$result[$id] = $value;
	}

}