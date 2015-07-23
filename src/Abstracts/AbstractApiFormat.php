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
    $output = new \stdClass();
    $output->status = new \stdClass();
    $output->status->code = $statusCode;
    $output->status->message = $statusMessage;
    $output->type = $type ? $type : get_class($result);

    //Ensure Valid Namespace
    if(substr($output->type, 0, 1) !== '\\')
    {
      $output->type = '\\' . $output->type;
    }

    $output->result = $result;

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

    $body = '';
    try
    {
      $body = (string)$raw->getBody();
      $result = $this->getDecoder()->decode($body, self::FORMAT);
    }
    catch(\Exception $e)
    {
      if(!empty($body))
      {
        $body = ' (' . $body . ')';
      }
      error_log("Invalid API Response: " . $body);
      throw new InvalidApiResponseException(
        "Unable to decode raw api response.", 500, $e
      );
    }

    if(
      !property_exists($result, 'type')
      || !property_exists($result, 'status')
      || !property_exists($result, 'result')
      || !property_exists($result->status, 'message')
      || !property_exists($result->status, 'code')
    )
    {
      error_log("Invalid API Result: " . json_encode($result));
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
        (float)str_replace([',', 'ms'], '', $totalTime),
        (float)str_replace([',', 'ms'], '', $executionTime),
        (float)str_replace([',', 'ms'], '', $callTime)
      )
    );
  }
}
