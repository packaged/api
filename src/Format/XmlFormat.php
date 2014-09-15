<?php
namespace Packaged\Api\Format;

use Packaged\Api\Abstracts\AbstractApiFormat;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

class XmlFormat extends AbstractApiFormat
{
  const FORMAT = 'xml';

  public function getEncoder()
  {
    return new XmlEncoder();
  }

  public function getDecoder()
  {
    return new XmlEncoder();
  }
}
