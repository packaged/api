<?php
namespace Packaged\Api\Abstracts;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Packaged\Api\Format\JsonFormat;
use Packaged\Api\Interfaces\ApiAwareInterface;
use Packaged\Api\Interfaces\ApiInterface;
use Packaged\Api\Interfaces\ApiRequestInterface;
use Packaged\Api\Interfaces\EndpointInterface;
use Packaged\Api\HttpVerb;

abstract class AbstractApi extends AbstractDefinable implements ApiInterface
{
  protected $_client;
  protected $_guzzleConfig = [];

  /**
   * Bind this API to an instance
   *
   * @param ApiAwareInterface $instance
   *
   * @return ApiAwareInterface
   */
  public function bind(ApiAwareInterface $instance)
  {
    $instance->setApi($this);
    return $instance;
  }

  /**
   * Retrieve the Guzzle HTTP Client
   *
   * @return Client
   */
  protected function _getClient()
  {
    if($this->_client === null)
    {
      $this->_client = new Client($this->_guzzleConfig);
    }

    return $this->_client;
  }

  /**
   * Build the base url for the endpoint
   *
   * @param EndpointInterface $endpointInterface
   *
   * @return string
   */
  protected function _buildEndpointUrl(EndpointInterface $endpointInterface)
  {
    return build_path_unix($this->getUrl(), $endpointInterface->getPath());
  }

  /**
   * @param \Packaged\Api\Interfaces\ApiRequestInterface $request
   *
   * @return \Packaged\Api\Interfaces\ApiResponseInterface
   */
  public function processRequest(ApiRequestInterface $request)
  {
    $apiRequest = $this->_createRequest($request);

    $time = microtime(true);
    try
    {
      $response = $this->_getClient()->send($apiRequest);
    }
    catch(ClientException $e)
    {
      $response = $e->getResponse();
    }
    $totalTime = microtime(true) - $time;

    $format = new JsonFormat();
    return $format->decode($response, number_format($totalTime * 1000, 3));
  }

  /**
   * @param ApiRequestInterface $request
   *
   * @return \GuzzleHttp\Message\RequestInterface
   */
  protected function _createRequest(ApiRequestInterface $request)
  {
    return $this->_getClient()->createRequest(
      $request->getVerb(),
      build_path_unix($this->getUrl(), $request->getPath())
      . $request->getQueryString(),
      $this->_makeOptions($request)
    );
  }

  /**
   * @param ApiRequestInterface $request
   *
   * @return array
   */
  protected function _makeOptions(ApiRequestInterface $request)
  {
    $options = [];

    if($request->getVerb() == HttpVerb::POST)
    {
      $options['body'] = $request->getPostData();
    }

    return $options;
  }
}
