<?php
namespace Packaged\Api\Abstracts;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Promise\PromiseInterface;
use Packaged\Api\Format\JsonFormat;
use Packaged\Api\HttpVerb;
use Packaged\Api\Interfaces\ApiAwareInterface;
use Packaged\Api\Interfaces\ApiInterface;
use Packaged\Api\Interfaces\ApiRequestInterface;
use Packaged\Api\Interfaces\EndpointInterface;
use Packaged\Helpers\Path;

abstract class AbstractApi extends AbstractDefinable implements ApiInterface
{
  protected $_client;

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
      $this->_client = new Client();
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
    return Path::unix($this->getUrl(), $endpointInterface->getPath());
  }

  /**
   * @param \Packaged\Api\Interfaces\ApiRequestInterface $request
   *
   * @return \Packaged\Api\Interfaces\ApiResponseInterface
   *
   * @throws \Packaged\Api\Exceptions\InvalidApiResponseException
   */
  public function processRequest(ApiRequestInterface $request)
  {
    return $this->processPreparedRequest($this->prepareRequest($request), $request);
  }

  /**
   * @param ApiRequestInterface $request
   *
   * @return PromiseInterface
   */
  public function prepareRequest(ApiRequestInterface $request)
  {
    return $this->_createRequest($request);
  }

  /**
   * @param PromiseInterface         $apiRequest
   *
   * @param ApiRequestInterface|null $rawRequest
   *
   * @return \Packaged\Api\Interfaces\ApiResponseInterface
   */
  public function processPreparedRequest(PromiseInterface $apiRequest, ApiRequestInterface $rawRequest = null)
  {
    $time = microtime(true);
    try
    {
      $response = $apiRequest->wait();
    }
    catch(ClientException $e)
    {
      $response = $e->getResponse();
    }

    $response = $this->_processResponse($response);
    $totalTime = microtime(true) - $time;
    return $this->_format($response, $totalTime, $rawRequest);
  }

  protected function _format($response, $totalTime = 0, ApiRequestInterface $rawRequest = null)
  {
    $format = new JsonFormat();
    return $format->decode($response, number_format($totalTime * 1000, 3));
  }

  /**
   * Process the raw response from api calls
   *
   * @param $response
   *
   * @return mixed
   */
  protected function _processResponse($response)
  {
    return $response;
  }

  /**
   * @param ApiRequestInterface $request
   *
   * @return PromiseInterface
   */
  protected function _createRequest(ApiRequestInterface $request)
  {
    $promise = $this->_getClient()->requestAsync(
      $request->getVerb(),
      Path::unix($this->getUrl(), $request->getPath()) . $request->getQueryString(),
      $this->_makeOptions($request)
    );
    $request->setPromise($promise);
    return $promise;
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
      $options['form_params'] = $request->getPostData();
    }

    return $options;
  }
}
