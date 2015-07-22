<?php
namespace Packaged\Api\Exceptions;

use Packaged\Api\Abstracts\AbstractApiFormat;

class ApiException extends \Exception
{
  public function getReturn()
  {
    return null;
  }

  public function getFormatted(AbstractApiFormat $formatter)
  {
    return $formatter->encode(
      $this->getReturn(),
      $this->getCode(),
      $this->getMessage(),
      '\\' . get_class($this)
    );
  }
}
