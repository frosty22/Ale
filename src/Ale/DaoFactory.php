<?php

namespace Ale;

/**
 *
 * @copyright Copyright (c) 2013 Ledvinka Vít
 * @author Ledvinka Vít, frosty22 <ledvinka.vit@gmail.com>
 *
 */
class DaoFactory extends \Nette\Object
{

	/**
	 * @var \Kdyby\Doctrine\EntityManager
	 */
	private $em;


	/**
	 * @param \Kdyby\Doctrine\EntityManager $em
	 */
	public function __construct(\Kdyby\Doctrine\EntityManager $em)
	{
		$this->em = $em;
	}


	/**
	 * @param string $entity
	 * @return \Kdyby\Doctrine\EntityDao
	 */
	public function create($entity)
	{
		return $this->em->getRepository($entity);
	}

}
