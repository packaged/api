<?php
namespace Packaged\Api\Interfaces;

interface ApiAwareInterface
{
  /**
   * Set the API for this class
   *
   * @param ApiInterface $api
   *
   * @return static
   */
  public function setApi(ApiInterface $api);

  /**
   * Retrieve the API from this class
   *
   * @return ApiInterface
   *
   * @throws \RuntimeException When no API is available
   */
  public function getApi();
}
