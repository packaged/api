<?php
namespace Packaged\Api\Abstracts;

use Packaged\Api\Interfaces\ApiPayloadInterface;
use Packaged\Api\Validation\PayloadValidator;
use Packaged\Helpers\ArrayHelper;
use Packaged\Helpers\Arrays;
use Packaged\Helpers\Objects;

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

  /**
   * @param array|object $source
   *
   * @return $this
   */
  public function hydrate($source)
  {
    if(is_object($source))
    {
      $source = Objects::propertyValues($source);
    }
    else
    {
      $source = (array)$source;
    }
    foreach(Objects::propertyValues($this) as $key => $value)
    {
      $this->$key = Arrays::value($source, $key, $value);
    }
    return $this;
  }
}
