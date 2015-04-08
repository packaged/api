<?php
namespace Packaged\Api\Abstracts;

use Packaged\Api\Interfaces\ApiPayloadInterface;

class AbstractApiPayload extends AbstractDefinable
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
}
