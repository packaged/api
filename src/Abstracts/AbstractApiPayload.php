<?php
namespace Packaged\Api\Abstracts;

use Packaged\Api\Interfaces\ApiPayloadInterface;
use Packaged\Api\Validation\PayloadValidator;

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
    return json_decode(json_encode(get_public_properties($this)), true);
  }

  public function validate(array $properties = null)
  {
    return (new PayloadValidator($this))->validate($properties);
  }
}
