<?php
namespace Packaged\Api\Interfaces;

interface EndpointInterface extends DefinableInterface
{
  /**
   * Retrieve the base path for the endpoint
   *
   * @return string
   */
  public function getBasePath();

  /**
   * Retrieve the path for the endpoint
   *
   * @return string
   */
  public function getPath();
}
