<?php
namespace Packaged\Api\Tests\Support;

use Packaged\Api\Abstracts\AbstractApiPayload;

class MockPayload extends AbstractApiPayload
{
  /**
   * @required
   */
  public $notNullField;

  public $key1;
  public $key2;
}
