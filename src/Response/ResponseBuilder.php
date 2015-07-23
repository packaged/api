<?php
namespace Packaged\Api\Response;

use Packaged\Api\Exceptions\ApiException;
use Packaged\Api\Interfaces\ApiResponseInterface;
use Packaged\Helpers\Objects;

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
        "Type Class '" . $type . "', could not be loaded"
      );
    }

    $interfaces = class_implements($type);
    if($type == '\Exception'
      || $type === '\Packaged\Api\Exceptions\ApiException'
      || in_array('\Packaged\Api\Exceptions\ApiException', $interfaces)
      || array_key_exists('Exception', class_parents($type))
    )
    {
      $code = $data->getStatusCode();
      if(!is_numeric($code))
      {
        $code = 500;
      }
      $exception = new $type($data->getStatusMessage(), $code);
      Objects::hydrate($exception, $data->getRawResult());
      throw $exception;
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
      throw new ApiException(
        "An invalid message type was used '" . $type . "'"
      );
    }
  }
}
