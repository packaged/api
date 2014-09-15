<?php
namespace Packaged\Api;

use Packaged\Api\Interfaces\ApiRequestInterface;
use Packaged\Api\Interfaces\ApiResponseInterface;
use Packaged\Api\Response\ApiCallData;

abstract class ApiResponse implements ApiResponseInterface
{
  /**
   * @param ApiRequestInterface $request
   *
   * @return static
   */
  public static function create(ApiRequestInterface $request)
  {
    return $request->get();
  }

  /**
   * @var ApiCallData
   */
  protected $_apiCallData;

  protected function _getProperty($property, $default = null)
  {
    return idp(
      $this->_apiCallData->getRawResult(),
      strtolower($property),
      $default
    );
  }

  /**
   * Retrieve the information about the call
   *
   * @return ApiCallData
   */
  public function getApiCallData()
  {
    return $this->_apiCallData;
  }

  /**
   * @param ApiCallData $callData
   *
   * @return ApiResponseInterface|static
   */
  public function setApiCallData(ApiCallData $callData)
  {
    $this->_apiCallData = $callData;
  }

  public function __call($method, $params)
  {
    if(starts_with($method, 'get'))
    {
      return $this->_getProperty(substr($method, 3), head($params));
    }
    else
    {
      throw new \Exception("Method $method is not supported");
    }
  }
}
