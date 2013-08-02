<?php

namespace Ale\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Base entity for all entities.
 *
 * @copyright Copyright (c) 2013 Ledvinka Vít
 * @author Ledvinka Vít, frosty22 <ledvinka.vit@gmail.com>
 *
 * @ORM\MappedSuperclass()
 *
 */
abstract class BaseEntity extends \Kdyby\Doctrine\Entities\BaseEntity
{

}
