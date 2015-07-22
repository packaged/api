<?php
namespace Packaged\Api\Tests;

use Packaged\Api\Tests\Support\MockPayload;

class ValidationTest extends \PHPUnit_Framework_TestCase
{
  /**
   * @expectedException \Packaged\Api\Exceptions\PayloadValidationException
   * @expectedExceptionMessage Property 'notNullField' with value '' is required
   * @expectedExceptionCode    400
   */
  public function testValidation()
  {
    $payload = new MockPayload();
    $payload->validate();
  }
}
