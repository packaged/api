<?php
namespace Packaged\ApiExample;

use Packaged\Api\ApiResponse;

/**
 * @method int getAge
 * @method string getUsername
 */
class UserResult extends ApiResponse
{
  public function getName()
  {
    return $this->_getProperty('name');
  }
}
