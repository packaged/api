<?php
namespace Packaged\Api\Response;

use Packaged\Api\Interfaces\ApiResponseInterface;

class ResponseBuilder
{
  /**
   * @param ApiCallData $data
   *
   * @return ApiResponseInterface
   *
   * @throws \Exception
   */
  public static function create(ApiCallData $data)
  {
    $type       = $data->getResponseType();
    $interfaces = class_implements($type);
    if(in_array('Packaged\Api\Interfaces\ApiExceptionInterface', $interfaces))
    {
      return new $type($data->getStatusCode(), $data->getStatusMessage());
    }
    else if(in_array(
      'Packaged\Api\Interfaces\ApiResponseInterface',
      $interfaces
    ))
    {
      $class = new $type;
      /**
       * @var $class \Packaged\Api\Interfaces\ApiResponseInterface
       */
      $class->setApiCallData($data);
      return $class;
    }
    else
    {
      throw new \Exception("An invalid message type was used '" . $type . "'");
    }
  }
}
