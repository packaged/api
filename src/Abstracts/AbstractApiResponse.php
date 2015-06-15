<?php
namespace Packaged\Api\Abstracts;

use Packaged\Api\Interfaces\ApiRequestInterface;
use Packaged\Api\Interfaces\ApiResponseInterface;
use Packaged\Api\Response\ApiCallData;
use Packaged\Helpers\Arrays;
use Packaged\Helpers\Objects;
use Packaged\Helpers\Strings;

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
    return Objects::property(
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
    return Objects::propertyValues($this);
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
    $data = $callData->getRawResult();
    $this->hydrate($data);
  }

  /**
   * Hydrate the public properties
   *
   * @param $data
   */
  public function hydrate($data)
  {
    if($data && $this->_hydratePublic)
    {
      foreach($this->toArray() as $key => $value)
      {
        if(is_array($data))
        {
          $this->$key = Arrays::value($data, $key, $value);
        }
        else if(is_object($data))
        {
          $this->$key = Objects::property($data, $key, $value);
        }
      }
    }
  }

  /**
   * Create a new response object and hydrate with data
   *
   * @param $data
   *
   * @return static
   */
  public static function make($data)
  {
    $response = new static;
    $response->hydrate($data);
    return $response;
  }

  public function __call($method, $params)
  {
    if(Strings::startsWith($method, 'get'))
    {
      return $this->_getProperty(substr($method, 3), Arrays::first($params));
    }
    else
    {
      throw new \Exception("Method $method is not supported");
    }
  }
}
