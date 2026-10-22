<?php
namespace Packaged\Api\Tests;

use Packaged\Api\Exceptions\PayloadValidationException;
use Packaged\Api\Tests\Support\MockPayload;
use PHPUnit\Framework\TestCase;

class ValidationTest extends TestCase
{
  public function testValidation()
  {
    $this->expectException(PayloadValidationException::class);
    $this->expectExceptionMessage("Property 'notNullField' with value '' is required");
    $this->expectExceptionCode(400);
    $payload = new MockPayload();
    $payload->validate();
  }
}
