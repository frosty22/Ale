<?php

namespace Ale;

abstract class Exception extends \Exception {  }

/** Abstract base exceptions */
abstract class RuntimeException extends \App\Exception {  }
abstract class LogicException extends \App\Exception {  }

/** Runtime exceptions */
class RelationFoundException extends \App\RuntimeException { }
class OutOfBoundsException extends \App\RuntimeException { }
class BadRequestException extends \App\RuntimeException { }
class NotFoundException extends \App\RuntimeException { }
class InvalidStateException extends  \App\RuntimeException { }
class UnexpectedValueException extends \App\RuntimeException {  }
class DuplicateEntryException extends \App\RuntimeException {
	public $value;
	public $property;
	public function __construct($property, $value) {
		$this->value = $value;
		$this->property = $property;
	}
}

/** Logic exceptions */
class InvalidArgumentException extends \App\LogicException { }
class ExceptedAnnotationException extends \App\LogicException { }
class InvalidPropertyException extends \App\LogicException {  }
class NotImplementException extends \App\LogicException {  }
class InvalidCallException extends \App\LogicException { }
class MissingClassException extends \App\LogicException { }
class MemberAccessException extends \App\LogicException { }
class MissingServiceException extends \App\LogicException {  }
