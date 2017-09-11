<?php
namespace Packaged\Api\Tests;

use Packaged\Api\Tests\Support\MockPayload;
use PHPUnit\Framework\TestCase;

class ValidationTest extends TestCase
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
