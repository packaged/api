<?php
namespace Packaged\Api\Interfaces;

use GuzzleHttp\Message\ResponseInterface;

interface ApiFormatInterface
{
  public function encode(
    $result, $statusCode = 200, $statusMessage = '', $type = null
  );

  /**
   * @param ResponseInterface $raw
   * @param int               $totalTime
   *
   * @return ApiResponseInterface
   */
  public function decode(ResponseInterface $raw, $totalTime = 0);
}
