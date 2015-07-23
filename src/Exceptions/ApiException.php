<?php
namespace Packaged\Api\Exceptions;

use Packaged\Api\Abstracts\AbstractApiFormat;

class ApiException extends \Exception
{
  public function getFormatted(AbstractApiFormat $formatter)
  {
    return $formatter->encode($this, $this->getCode(), $this->getMessage());
  }
}
