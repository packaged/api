<?php
namespace Packaged\Api\Abstracts;

use Packaged\Api\Interfaces\ApiPayloadInterface;
use Packaged\Api\Validation\PayloadValidator;
use Packaged\Helpers\ArrayHelper;

abstract class AbstractApiPayload extends AbstractDefinable
  implements ApiPayloadInterface
{
  /**
   * Retrieve the request data as an array
   *
   * @return array
   */
  public function toArray()
  {
    return ArrayHelper::toArray($this);
  }

  public function validate(array $properties = null)
  {
    return (new PayloadValidator($this))->validate($properties);
  }
}
