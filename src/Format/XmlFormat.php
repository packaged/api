<?php
namespace Packaged\Api\Format;

use Packaged\Api\Abstracts\AbstractApiFormat;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

class XmlFormat extends AbstractApiFormat
{
  const FORMAT = 'xml';

  public function _getEncoder()
  {
    return new XmlEncoder();
  }

  public function _getDecoder()
  {
    return new XmlEncoder();
  }
}
