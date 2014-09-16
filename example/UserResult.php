<?php
namespace Packaged\ApiExample;

use Packaged\Api\Abstracts\AbstractApiResponse;

/**
 * @method int getAge
 * @method string getUsername
 */
class UserResult extends AbstractApiResponse
{
  public function getName()
  {
    return $this->_getProperty('name');
  }
}
