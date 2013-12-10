<?php

namespace Ale\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Named entity.
 *
 * @copyright Copyright (c) 2013 Ledvinka Vít
 * @author Ledvinka Vít, frosty22 <ledvinka.vit@gmail.com>
 * @author Filip Procházka <filip@prochazka.su>
 *
 * @ORM\MappedSuperclass()
 *
 * @method string getName()
 * @method setName(string $name)
 *
 */
abstract class NamedEntity extends IdentifiedEntity
{


	/**
	 * @ORM\Column(type="string")
	 */
	protected $name;

}
