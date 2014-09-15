<?php
namespace Packaged\ApiExample\Endpoints;

use Packaged\Api\Abstracts\AbstractEndpoint;
use Packaged\Api\HttpVerb;
use Packaged\Api\Interfaces\ApiRequestInterface;
use Packaged\ApiExample\Requests\UserPayload;

class UserEndpoint extends AbstractEndpoint
{
  protected $_path = 'post';

  /**
   * @param UserPayload $request
   *
   * @return ApiRequestInterface
   */
  public function create(UserPayload $request)
  {
    return $this->_createRequest($request);
  }

  public function get($userId)
  {
  }

  public function all()
  {
    return $this->_createRequest(null, null, HttpVerb::POST);
  }
}
