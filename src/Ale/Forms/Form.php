<?php

namespace Ale\Forms;

use Ale\Entities\BaseEntity;
use EntityMetaReader\EntityReader;
use Vodacek\Forms\Controls\DateInput;

/**
 *
 * @copyright Copyright (c) 2013 Ledvinka Vít
 * @author Ledvinka Vít, frosty22 <ledvinka.vit@gmail.com>
 *
 * @method DateInput addDate(string $name, string $label, string $type)
 * @method \Kdyby\Replicator\Container addDynamic(string $name, callback $callback)
 *
 */
class Form extends \Ale\Application\UI\Form {


	/**
	 * @var EntityReader
	 */
	private $reader;


	/**
	 * @var \Nette\Security\User
	 */
	private $user;


	/**
	 * @var DataFetcher
	 */
	private $fetcher;


	/**
	 * @param EntityReader $reader
	 * @param \Nette\Security\User $user
	 * @param DataFetcher $fetcher
	 */
	public function __construct(EntityReader $reader, \Nette\Security\User $user, DataFetcher $fetcher)
	{
		parent::__construct();
		$this->reader = $reader;
		$this->user = $user;
		$this->fetcher = $fetcher;
	}


	/**
	 * Add all properties of entity
	 * @param string $entity
	 * @return $this
	 */
	public function addEntity($entity)
	{
		$columns = $this->reader->getEntityColumns($entity);
		foreach ($columns as $column) {
			if ($column->getAnnotation('Doctrine\ORM\Mapping\GeneratedValue') !== NULL)
				continue;

			$access = $column->getAnnotation('EntityMetaReader\Mapping\Access', TRUE);
			/** @var \EntityMetaReader\Mapping\Access $access */

			$writeable = $this->user ? $access->checkWriteAccess($this->user) : $access->isWritable();
			if (!$writeable) continue;

			try {
				$this->addEntityColumn($column);
			} catch (\Ale\ExceptedAnnotationException $e) {
			}

		}

		return $this;
	}


	/**
	 * @param mixed $values
	 * @param bool $erase
	 * @return \Nette\Forms\Container
	 */
	public function setDefaults($values, $erase = FALSE)
	{
		if ($values instanceof BaseEntity) {
			$defaults = array();
			$columns = $this->reader->getEntityColumns(get_class($values));
			foreach ($columns as $name => $columnReader) {
				if ($columnReader->isValueType()) {
					$defaults[$name] = $values->$name;
				}
				elseif ($columnReader->isEntityType()) {
					$defaults[$name] = $values->$name ? $values->$name->id : NULL;
				}
				elseif ($columnReader->isCollectionType()) {
					$targetEntity = $columnReader->getTargetEntity();
					// TODO: ?
				}

			}

			$values = $defaults;
		}

		return parent::setDefaults($values, $erase);
	}


	/**
	 * Add entity property like control
	 * @param string $entity
	 * @param string $property
	 * @param string|NULL $name
	 * @throws \Ale\InvalidArgumentException
	 * @return \Nette\Forms\IControl
	 */
	public function addEntityProperty($entity, $property, $name = NULL)
	{
		$columns = $this->reader->getEntityColumns($entity);
		if (!isset($columns[$property]))
			throw new \Ale\InvalidArgumentException("Property with name '$property' is not ORM property of entity");

		return $this->addEntityColumn($columns[$property], $name);
	}


	/**
	 * @param \EntityMetaReader\ColumnReader $columnReader
	 * @param null|string $name
	 * @return \Nette\Forms\Controls\Checkbox|\Nette\Forms\Controls\TextArea|\Nette\Forms\Controls\TextInput|null|DateInput
	 * @throws \Ale\UnexpectedValueException
	 * @throws \Ale\InvalidArgumentException
	 * @throws \Ale\ExceptedAnnotationException
	 */
	protected function addEntityColumn(\EntityMetaReader\ColumnReader $columnReader, $name = NULL)
	{
		if ($columnReader->getAnnotation('Doctrine\ORM\Mapping\GeneratedValue') !== NULL)
			throw new \Ale\InvalidArgumentException('Property ' . $columnReader->getName() . ' cannot be added,
													because it is generated value.');

		$label = $columnReader->getAnnotation('EntityMetaReader\Mapping\Name', TRUE, $columnReader->getName());
		/** @var \EntityMetaReader\Mapping\Name $name */

		$label = $label->getName();
		$name = $name ? $name : $columnReader->getName();

		if ($column = $columnReader->getAnnotation('Doctrine\ORM\Mapping\Column')) {
			/** @var \Doctrine\ORM\Mapping\Column $column */
			switch ($column->type) {
				case "string":
					$control = $this->addText($name, $label);
					break;
				case "integer":
				case "smallint":
				case "bigint":
					$control = $this->addText($name, $label);
					$control->addRule(self::INTEGER, "Položka " . mb_strtolower($label) . " musí být celé číslo.");
					break;
				case "boolean":
					$control = $this->addCheckbox($name, $label);
					break;
				case "decimal":
				case "float":
					$control = $this->addText($name, $label);
					$control->addRule(self::FLOAT, "Položka " . mb_strtolower($label) . " musí být číslo.");
					break;
				case "date":
					$control = $this->addDate($name, $label, DateInput::TYPE_DATE);
					break;
				case "datetime":
					$control = $this->addDate($name, $label, DateInput::TYPE_DATETIME);
					break;
				case "time":
					$control = $this->addDate($name, $label, DateInput::TYPE_TIME);
					break;
				case "text":
					$control = $this->addTextArea($name, $label);
					break;
				default:
					throw new \Ale\UnexpectedValueException("Column type '{$column->type}' is not supported.");
			}

			if (!$columnReader->isNullable() && !$control instanceof \Nette\Forms\Controls\Checkbox) {
				$control->setRequired("Prosím vyplňte položku: " . $label);
			}

			$control->setDefaultValue($columnReader->getDefault());

		} elseif ($columnReader->isEntityType()) {
			$targetEntity = $columnReader->getTargetEntity();

			$relation = $columnReader->getAnnotation('Ale\Forms\Mapping\Relation');
			if (!$relation)
				throw new \Ale\ExceptedAnnotationException('Excepted annotation "Ale\Forms\Mapping\Relation" on property.');

			$control = $this->addSelect($name, $label, $this->fetcher->fetchPairs($relation, $targetEntity))
					->setPrompt("Vybrat");

			if (!$columnReader->isNullable())
				$control->setRequired("Vyberte prosím položku: " . $label);

		} elseif ($columnReader->isCollectionType()) {
			$targetEntity = $columnReader->getTargetEntity();
			// TODO: ?

		} else
			throw new \Ale\UnexpectedValueException("Invalid property type.");

		return isset($control) ? $control : NULL;
	}




}