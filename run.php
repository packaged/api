<?php
require 'vendor/autoload.php';
require 'test.php';

function rnd(\Packaged\Api\Abstracts\AbstractDefinable $item)
{
  $return = '';
  $return .= "\n";
  $return .= "[" . get_class($item) . "]\n";
  $return .= "Name: " . $item->getName() . "\n";
  $return .= "Description: " . $item->getDescription() . "\n";
  $return .= "\n";
  return $return;
}
