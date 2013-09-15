<?php

namespace Ale;

abstract class Exception extends \Exception {  }

/** Abstract base exceptions */
abstract class RuntimeException extends Exception {  }
abstract class LogicException extends Exception {  }

/** Runtime exceptions */
class RelationFoundException extends RuntimeException { }
class OutOfBoundsException extends RuntimeException { }
class BadRequestException extends RuntimeException { }
class NotFoundException extends RuntimeException { }
class InvalidStateException extends  RuntimeException { }
class UnexpectedValueException extends RuntimeException {  }
class DuplicateEntryException extends RuntimeException {
	public $value;
	public $property;
	public function __construct($property, $value) {
		$this->value = $value;
		$this->property = $property;
	}
}

/** Logic exceptions */
class InvalidArgumentException extends LogicException { }
class ExceptedAnnotationException extends LogicException { }
class InvalidPropertyException extends LogicException {  }
class NotImplementException extends LogicException {  }
class InvalidCallException extends LogicException { }
class MissingClassException extends LogicException { }
class MemberAccessException extends LogicException { }
class MissingServiceException extends LogicException {  }
