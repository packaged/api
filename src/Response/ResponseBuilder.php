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
    $type = $data->getResponseType();

    if(!class_exists($type))
    {
      throw new \Exception(
        "An type '" . $type . "', class could not be loaded"
      );
    }

    $interfaces = class_implements($type);
    if($type === '\Packaged\Api\Exceptions\ApiException'
      || in_array('\Packaged\Api\Exceptions\ApiException', $interfaces)
      || array_key_exists('Exception', class_parents($type))
    )
    {
      throw new $type($data->getStatusMessage(), $data->getStatusCode());
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
