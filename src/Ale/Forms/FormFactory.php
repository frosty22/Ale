<?php

namespace Ale\Forms;

use EntityMetaReader\EntityReader;
use Nette\Security\User;

/**
 *
 * @copyright Copyright (c) 2013 Ledvinka Vít
 * @author Ledvinka Vít, frosty22 <ledvinka.vit@gmail.com>
 *
 */
class FormFactory extends \Nette\Object {


	/**
	 * @var EntityReader
	 */
	private $reader;


	/**
	 * @var User
	 */
	private $user;


	/**
	 * @var DataFetcher
	 */
	private $fetcher;


	/**
	 * @param EntityReader $reader
	 * @param User $user
	 * @param DataFetcher $fetcher
	 */
	public function __construct(EntityReader $reader, User $user, DataFetcher $fetcher)
	{
		$this->reader = $reader;
		$this->user = $user;
		$this->fetcher = $fetcher;

		\Vodacek\Forms\Controls\DateInput::register();
	}


	/**
	 * @return Form
	 */
	public function create()
	{
		return new Form($this->reader, $this->user, $this->fetcher);
	}

}