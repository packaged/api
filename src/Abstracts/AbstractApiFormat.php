<?php
namespace Packaged\Api\Abstracts;

use GuzzleHttp\Message\ResponseInterface;
use Packaged\Api\Exceptions\InvalidApiResponseException;
use Packaged\Api\Interfaces\ApiFormatInterface;
use Packaged\Api\Response\ApiCallData;
use Packaged\Api\Response\ResponseBuilder;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;

abstract class AbstractApiFormat implements ApiFormatInterface
{
  const FORMAT = 'abstract';

  /**
   * @return EncoderInterface
   */
  abstract protected function getEncoder();

  /**
   * @return DecoderInterface
   */
  abstract protected function getDecoder();

  public function encode(
    $result, $statusCode = 200, $statusMessage = '', $type = null
  )
  {
    $output                  = new \stdClass();
    $output->status          = new \stdClass();
    $output->status->code    = $statusCode;
    $output->status->message = $statusCode;
    $output->type            = $type ? $type : get_class($result);
    $output->result          = $result;

    return $this->getEncoder()->encode($output, '');
  }

  public function decode(ResponseInterface $raw, $totalTime = 0)
  {
    $executionTime = $callTime = 0;

    if($raw->hasHeader('X-Execution-Time'))
    {
      $executionTime = $raw->getHeader('X-Execution-Time');
    }

    if($raw->hasHeader('X-Call-Time'))
    {
      $callTime = $raw->getHeader('X-Call-Time');
    }

    try
    {
      $result = $this->getDecoder()->decode(
        (string)$raw->getBody(),
        self::FORMAT
      );
    }
    catch(\Exception $e)
    {
      throw new InvalidApiResponseException(
        "Unable to decode raw api response",
        500
      );
    }

    if(!isset(
      $result->type,
      $result->status,
      $result->status->message,
      $result->status->code,
      $result->result)
    )
    {
      throw new InvalidApiResponseException("Invalid api result", 500);
    }

    if($executionTime === 0)
    {
      $executionTime = $totalTime;
    }

    if($callTime === 0)
    {
      $callTime = $executionTime;
    }

    return ResponseBuilder::create(
      ApiCallData::create(
        $result->type,
        $result->result,
        $result->status->code,
        $result->status->message,
        (float)$totalTime,
        (float)$executionTime,
        (float)$callTime
      )
    );
  }
}
