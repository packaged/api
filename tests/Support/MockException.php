<?php
namespace Packaged\Api\Tests\Support;

use Packaged\Api\Exceptions\ApiException;

class MockException extends ApiException
{
  public $errorValue;
}
