<?php
namespace Packaged\Api\Format;

use Packaged\Api\Abstracts\AbstractApiFormat;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncode;

class JsonFormat extends AbstractApiFormat
{
  const FORMAT = 'json';

  public function getEncoder()
  {
    return new JsonEncode();
  }

  public function getDecoder()
  {
    return new JsonDecode();
  }
}
