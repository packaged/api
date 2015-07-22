<?php
namespace Packaged\Api\Exceptions;

class PayloadValidationException extends ApiException
{
  public $property;
  public $value;

  public static function create($property, $value, $message, $code = 400)
  {
    $msg = "Property '$property' with value '$value' $message";
    $e = new static($msg, $code);
    $e->property = $property;
    $e->value = $value;
    return $e;
  }
}
