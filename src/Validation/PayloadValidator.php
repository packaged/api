<?php
namespace Packaged\Api\Validation;

use Packaged\Api\Abstracts\AbstractApiPayload;
use Packaged\Api\Exceptions\PayloadValidationError AS PVE;
use Packaged\DocBlock\DocBlockParser;
use Packaged\Helpers\Numbers;
use Packaged\Helpers\Objects;
use Packaged\Helpers\ValueAs;

class PayloadValidator
{
  /**
   * @var AbstractApiPayload
   */
  protected $_payload;
  protected $_properties;
  protected $_errors;
  protected $_repair;

  public function __construct(AbstractApiPayload $payload, $repair = true)
  {
    $this->_payload = $payload;
    $this->_repair = $repair;
  }

  public function validate(array $properties = null, $throw = true)
  {
    $allValid = true;

    if($properties === null)
    {
      $properties = Objects::properties($this->_payload);
    }

    $this->_properties = $properties;

    foreach($properties as $property)
    {
      $block = DocBlockParser::fromProperty($this->_payload, $property);
      $nullable = $block->hasTag('nullable');
      $optional = $block->hasTag('optional');
      $val = $this->_payload->$property;

      if(($val === null && ($nullable || $optional))
        || ($val === '' && $optional)
      )
      {
        continue;
      }

      foreach($block->getTags() as $tag => $tags)
      {
        foreach($tags as $opt)
        {
          if($this->_repair)
          {
            $this->repairValue($tag, $property, $val, $opt);
          }
          try
          {
            $this->runValidator($tag, $property, $val, $opt);
          }
          catch(\Exception $e)
          {
            if($throw)
            {
              throw $e;
            }
            else
            {
              $allValid = false;
              if(!isset($this->_errors[$property]))
              {
                $this->_errors[$property] = [];
              }
              $this->_errors[$property][] = $e->getMessage();
            }
          }
        }
      }
    }

    return $allValid;
  }

  public function repairValue($tag, $property, $value, $options)
  {
    switch(strtolower($tag))
    {
      case 'bool':
        $validBools = ['true', '1', 1, true, 'false', '0', 0, false];
        if(in_array($value, $validBools))
        {
          $value = ValueAs::bool($value);
        }
        break;
      case 'int':
        if(strlen((int)$value) == strlen($value))
        {
          $value = (int)$value;
        }
        break;
      case 'float':
        if((float)$value == strlen($value))
        {
          $value = (float)$value;
        }
        break;
    }
    $this->_payload->$property = $value;
  }

  public function runValidator($tag, $property, $value, $options)
  {
    $msg = null;
    switch(strtolower($tag))
    {
      case 'bool':
        if(!is_bool($value))
        {
          $msg = "is not a valid bool";
        }
        break;
      case 'int':
        if(!is_integer($value))
        {
          $msg = "is not a valid integer";
        }
        break;
      case 'float':
        if(!is_float($value))
        {
          $msg = "is not a valid float";
        }
        break;
      case 'scalar':
        if(!is_scalar($value))
        {
          $msg = "is not a valid float";
        }
        break;
      case 'length':
        list($low, $high) = explode(' ', $options, 2);
        if(!Numbers::between($value, (int)$low, (int)$high))
        {
          $msg = "is not between " . (int)$low . ' and ' . (int)$high;
        }
        break;
      case 'email':
        if(!filter_var($value, FILTER_VALIDATE_EMAIL))
        {
          $msg = "is not a valid email address";
        }
        else if(stristr($options, 'dns'))
        {
          list(, $host) = explode('@', $value, 2);
          if(!checkdnsrr($host))
          {
            $value = $host;
            $msg = "is not a valid email domain";
          }
        }
        break;
      case 'date':
        $timestamp = strtotime($value);
        if(date('Y-m-d', $timestamp) != $value)
        {
          $msg = 'is not a valid date';
        }
        break;
      case 'timestamp':
        if(!((string)
          (int)$value === (string)$value
          && ($value <= PHP_INT_MAX)
          && ($value >= ~PHP_INT_MAX))
        )
        {
          $msg = 'is not a valid timestamp';
        }
        break;
      case 'percent':
        if(Numbers::between($value, 0, 100))
        {
          $msg = 'is not a valid percentage';
        }
        break;
    }

    if($msg !== null)
    {
      throw PVE::create($property, $value, $msg);
    }

    return null;
  }
}
