<?php
namespace Packaged\Api\Interfaces;

interface DefinableInterface
{
  /**
   * Retrieve the name for this class
   *
   * @return string
   */
  public function getName();

  /**
   * Retrieve the description for this class
   *
   * @return string
   */
  public function getDescription();
}
