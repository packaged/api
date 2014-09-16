<?php
namespace Packaged\Api\Abstracts;

use Packaged\Api\Interfaces\ApiRequestInterface;
use Packaged\Api\Interfaces\ApiResponseInterface;
use Packaged\Api\Response\ApiCallData;
use Packaged\Helpers\ValueAs;

abstract class AbstractApiResponse implements ApiResponseInterface
{
  protected $_hydratePublic = true;

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
   * Retrieve the response data as an array
   *
   * @return array
   */
  public function toArray()
  {
    return get_public_properties($this);
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
    if($this->_hydratePublic)
    {
      $data = $callData->getRawResult();
      foreach(ValueAs::arr($data) as $key => $value)
      {
        $this->$key = $value;
      }
    }
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
