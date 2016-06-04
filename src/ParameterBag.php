<?php
declare(strict_types=1);
namespace Tiimber;

use ArrayIterator;
use IteratorAggregate;
use Serializable;

class ParameterBag implements IteratorAggregate, Serializable
{
  /**
   * Get object parameter
   *
   * @param $key String
   * @param $default mixed
   * @return mixed
   */
  public function get($key, $default = null)
  {
    if (isset($this->{$key})) {
      return $this->{$key};
    }
    return $default;
  }

  /**
   * Set object parameter
   *
   * @param $key String
   * @param $value mixed
   */
  public function set($key, $value)
  {
    $this->{$key} = $value;
  }

  /**
   * @return ArrayIterator
   */
  public function getIterator()
  {
    return new ArrayIterator($this->properties);
  }

  protected $properties;

  protected $private_properties = [];

  public function __construct($properties = null)
  {
    $this->properties = (object)$properties;
  }

  public function __set(string $property, $value): ParameterBag
  {
    if (!in_array($property, $this->private_properties)) {
      $this->properties->$property = $value;
    }
    return $this;
  }

  public function __get(string $property)
  {
    if (!in_array($property, $this->private_properties)) {
      return $this->properties->$property;
    }
  }

  public function __isset(string $property): bool
  {
    return isset($this->properties->$property);
  }

  public function serialize(): string
  {
    return json_encode($this->properties);
  }

  public function unserialize($data): ParameterBag
  {
    $this->properties = json_decode($data);
    return $this;
  }
}
