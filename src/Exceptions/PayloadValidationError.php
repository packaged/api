<?php
namespace Packaged\Api\Exceptions;

class PayloadValidationError extends \Exception
{
  public static function create($property, $value, $message, $code = 400)
  {
    $msg = "$property value '$value' $message";
    return new static($msg, $code);
  }
}
