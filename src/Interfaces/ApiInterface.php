<?php
namespace Packaged\Api\Interfaces;

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
   * Bind this API to an instance
   *
   * @param ApiAwareInterface $instance
   *
   * @return ApiAwareInterface
   */
  public function bind(ApiAwareInterface $instance);
}
