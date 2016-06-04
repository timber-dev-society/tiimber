<?php
declare(strict_types=1);
namespace Tiimber;

use ArrayIterator;
use IteratorAggregate;
use Serializable;

class ParameterBag implements IteratorAggregate, Serializable
{
  private $properties;

  private $private_properties = [];

  public function __construct($properties = null)
  {
    $this->properties = is_null($properties) ? null : (object)$properties;
  }

  /**
   * Get object parameter
   *
   * @param $key String
   * @param $default mixed
   * @return mixed
   */
  public function get(string $key, $default = null)
  {
    if (isset($this->properties->{$key})) {
      return $this->properties->{$key};
    }
    return $default;
  }

  /**
   * Set object parameter
   *
   * @param $key String
   * @param $value mixed
   */
  public function set(string $key, $value)
  {
    $this->properties->{$key} = $value;
  }

  /**
   * @return ArrayIterator
   */
  public function getIterator(): ArrayIterator
  {
    return new ArrayIterator($this->properties);
  }

  public function __isset(string $property): bool
  {
    return isset($this->properties->{$property});
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
