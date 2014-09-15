<?php
namespace Packaged\Api\Interfaces;

interface ApiPayloadInterface extends DefinableInterface
{
  /**
   * Retrieve the payload as an array
   *
   * @return array
   */
  public function toArray();
}
