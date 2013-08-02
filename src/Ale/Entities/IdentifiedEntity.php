<?php

namespace Ale\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Identified entities.
 *
 * @copyright Copyright (c) 2013 Ledvinka Vít
 * @author Ledvinka Vít, frosty22 <ledvinka.vit@gmail.com>
 *
 * @ORM\MappedSuperclass()
 *
 * @property-read int $id
 *
 */
abstract class IdentifiedEntity extends BaseEntity
{


	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 * @var integer
	 */
	protected $id;


	/**
	 * @return integer
	 */
	final public function getId()
	{
		if ($this instanceof Proxy && !$this->__isInitialized__ && !$this->id) {
			$identifier = $this->getReflection()->getProperty('_identifier');
			$identifier->setAccessible(TRUE);
			$id = $identifier->getValue($this);
			$this->id = reset($id);
		}

		return $this->id;
	}


}
