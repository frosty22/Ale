<?php

namespace Ale\Forms;
use Ale\Forms\Mapping\Relation;
use Kdyby\Doctrine\EntityManager;

/**
 *
 * @copyright Copyright (c) 2013 Ledvinka Vít
 * @author Ledvinka Vít, frosty22 <ledvinka.vit@gmail.com>
 *
 */
class DataFetcher extends \Nette\Object {


	/**
	 * @var EntityManager
	 */
	private $entityManager;


	/**
	 * @param EntityManager $em
	 */
	public function __construct(EntityManager $em)
	{
		$this->entityManager = $em;
	}


	/**
	 * @param Relation $relation
	 * @param $entityName
	 * @return array
	 */
	public function fetchPairs(Relation $relation, $entityName)
	{
		$criteria = array();
		return $this->entityManager->getDao($entityName)->findPairs($criteria, $relation->getSelectedBy());
	}


}