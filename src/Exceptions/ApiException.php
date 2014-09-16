<?php
namespace Packaged\Api\Exceptions;

class ApiException extends \Exception
{
  public function getReturn()
  {
    return null;
  }
}
