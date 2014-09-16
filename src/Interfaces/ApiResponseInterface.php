<?php
namespace Packaged\Api\Interfaces;

use Packaged\Api\Response\ApiCallData;

interface ApiResponseInterface
{
  /**
   * Retrieve the information about the call
   *
   * @return ApiCallData
   */
  public function getApiCallData();

  /**
   * @param ApiCallData $callData
   *
   * @return ApiResponseInterface|static
   */
  public function setApiCallData(ApiCallData $callData);

  /**
   * Retrieve the response data as an array
   *
   * @return array
   */
  public function toArray();
}
