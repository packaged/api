<?php
namespace Packaged\Api\Interfaces;

use GuzzleHttp\Promise\PromiseInterface;

interface ApiInterface extends DefinableInterface
{
  /**
   * Retrieve the url for the API
   *
   * @return string
   */
  public function getUrl();

  /**
   * @param \Packaged\Api\Interfaces\ApiRequestInterface $request
   *
   * @return \Packaged\Api\Interfaces\ApiResponseInterface
   */
  public function processRequest(ApiRequestInterface $request);

  /**
   * @param \Packaged\Api\Interfaces\ApiRequestInterface $request
   *
   * @return PromiseInterface
   */
  public function prepareRequest(ApiRequestInterface $request);

  /**
   * @param PromiseInterface         $apiRequest
   *
   * @param ApiRequestInterface|null $rawRequest
   *
   * @return \Packaged\Api\Interfaces\ApiResponseInterface
   */
  public function processPreparedRequest(PromiseInterface $apiRequest, ApiRequestInterface $rawRequest = null);

  /**
   * Bind this API to an instance
   *
   * @param ApiAwareInterface $instance
   *
   * @return ApiAwareInterface
   */
  public function bind(ApiAwareInterface $instance);
}
