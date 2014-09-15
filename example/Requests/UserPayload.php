<?php
namespace Packaged\ApiExample\Requests;

use Packaged\Api\Abstracts\AbstractApiPayload;

class UserPayload extends AbstractApiPayload
{
  public $name;
  public $age;

  public static function create($name, $age)
  {
    $new       = new static;
    $new->name = $name;
    $new->age  = $age;
    return $new;
  }
}
