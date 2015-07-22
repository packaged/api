<?php
namespace Packaged\Api\Tests\Support;

use Packaged\Api\Abstracts\AbstractApiResponse;

class MockResponse extends AbstractApiResponse
{
  public $notNullField;

  public $key1;
  public $key2;
}
