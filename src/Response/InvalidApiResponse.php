<?php
namespace Packaged\Api\Response;

use Packaged\Api\Abstracts\AbstractApiResponse;

class InvalidApiResponse extends AbstractApiResponse
{
  public $message;
  public $originalResponse;

  public static function withMessage($message, $original = null)
  {
    $resp = new static();
    $resp->message = $message;
    $resp->originalResponse = $original;
    return $resp;
  }
}
