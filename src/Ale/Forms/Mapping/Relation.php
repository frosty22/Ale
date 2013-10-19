<?php

namespace Ale\Forms\Mapping;

/**
 *
 * @Annotation
 * @copyright Copyright (c) 2013 Ledvinka Vít
 * @author Ledvinka Vít, frosty22 <ledvinka.vit@gmail.com>
 *
 */
class Relation {


	/**
	 * @var string
	 */
	private $selectedBy;


	/**
	 * @param array $args
	 * @throws \Ale\InvalidArgumentException
	 */
	public function __construct(array $args)
	{
		if (empty($args["selectedBy"]))
			throw new \Ale\InvalidArgumentException('Annotation argument "selectedBy" cannot be NULL.');

		$this->selectedBy = $args["selectedBy"];
	}

	/**
	 * @return string
	 */
	public function getSelectedBy()
	{
		return $this->selectedBy;
	}







}