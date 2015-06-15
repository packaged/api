<?php
namespace Packaged\Api\Abstracts;

use Packaged\Api\Interfaces\DefinableInterface;
use Packaged\DocBlock\DocBlockParser;
use Packaged\Helpers\Objects;
use Packaged\Helpers\Strings;

abstract class AbstractDefinable implements DefinableInterface
{
  /**
   * Retrieve the name for this endpoint
   *
   * @return string
   */
  public function getName()
  {
    return Strings::titleize(Objects::classShortname(get_called_class()));
  }

  /**
   * Retrieve the description for this endpoint
   *
   * @return string
   */
  public function getDescription()
  {
    return DocBlockParser::fromObject($this)->getSummary();
  }
}
