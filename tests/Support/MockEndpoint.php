<?php
namespace Packaged\Api\Tests\Support;

use Packaged\Api\Abstracts\AbstractEndpoint;
use Packaged\Api\Interfaces\ApiPayloadInterface;

class MockEndpoint extends AbstractEndpoint
{
  /**
   * Retrieve the path for the endpoint
   *
   * @return string
   */
  public function getPath()
  {
    return '';
  }

  public function getRequest(
    ApiPayloadInterface $payload = null, $path = '/', $verb = null
  )
  {
    return $this->_createRequest($payload, $path, $verb);
  }
}
