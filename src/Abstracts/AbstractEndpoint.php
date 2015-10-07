<?php
namespace Packaged\Api\Abstracts;

use Packaged\Api\Interfaces\ApiAwareInterface;
use Packaged\Api\Interfaces\ApiInterface;
use Packaged\Api\Interfaces\ApiPayloadInterface;
use Packaged\Api\ApiRequest;
use Packaged\Api\Interfaces\EndpointInterface;
use Packaged\Api\HttpVerb;
use Packaged\Api\Traits\ApiAwareTrait;
use Packaged\Helpers\Objects;
use Packaged\Helpers\Path;

abstract class AbstractEndpoint extends AbstractDefinable
  implements EndpointInterface, ApiAwareInterface
{
  protected $_path = '/';

  use ApiAwareTrait;

  /**
   * Create an instance of this endpoint already bound to the API
   *
   * @param ApiInterface $api
   *
   * @return static
   */
  public static function bound(ApiInterface $api)
  {
    $args = func_get_args();
    array_shift($args);
    $new = Objects::create(get_called_class(), $args);
    /**
     * @var $new ApiAwareInterface
     */
    $api->bind($new);
    return $new;
  }

  /**
   * Retrieve the base path for the endpoint
   *
   * @return string
   */
  public function getBasePath()
  {
    return $this->_path;
  }

  /**
   * Build a path, allowing :var replacements from the request object
   *
   * @param                     $path
   * @param ApiPayloadInterface $payload
   *
   * @return mixed
   */
  protected function _buildPath($path, ApiPayloadInterface $payload = null)
  {
    if(stristr($path, ':') && $payload !== null)
    {
      $find = array_map(
        function ($value)
        {
          return ':' . $value;
        },
        array_keys($payload->toArray())
      );
      return str_replace($find, $payload->toArray(), $path);
    }
    return $path;
  }

  /**
   * @param ApiPayloadInterface $payload
   * @param null                $path
   * @param null                $verb
   *
   * @return ApiRequest
   */
  protected function _createRequest(
    ApiPayloadInterface $payload = null, $path = null, $verb = null
  )
  {
    if($path === null)
    {
      $path = $this->_path;
    }
    else if(substr($path, 0, 1) !== '/')
    {
      $path = Path::buildUnix($this->_path, $path);
    }

    $path = $this->_buildPath($path, $payload);

    if($payload === null)
    {
      $request = ApiRequest::create($verb ? $verb : HttpVerb::GET, $path);
    }
    else if($verb === HttpVerb::GET)
    {
      $request = ApiRequest::create(
        HttpVerb::GET,
        $path,
        [],
        $payload->toArray()
      );
    }
    else
    {
      $request = ApiRequest::create(
        $verb ?: HttpVerb::POST,
        $path,
        $payload->toArray()
      );
    }
    $request->setApi($this->getApi());
    return $request;
  }
}
