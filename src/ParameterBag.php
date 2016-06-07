<?php
declare(strict_types=1);
namespace Tiimber;

use ArrayIterator;
use IteratorAggregate;
use Serializable;
use stdClass;

use Tiimber\Exception;

class ParameterBag implements IteratorAggregate, Serializable
{
  private $properties;

  private $private_properties = [];

  public function __construct($properties = null)
  {
    $this->properties = is_null($properties) ? new stdClass() : (object)$properties;
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
   * @return ParameterBag
   */
  public function set(string $key, $value): ParameterBag
  {
    if (is_object($value) && !$value instanceof Serializable) {
      throw new Exception('All object stored in a ParameterBag must be serializable and implement Serializable interface', 500);
    }
    $this->properties->{$key} = $value;
    return $this;
  }

  /**
   * Has object parameter
   *
   * @param $key String
   * @return boll
   */
  public function has(string $key): bool
  {
    return isset($this->properties->{$key});
  }

  /**
   * @return ArrayIterator
   */
  public function getIterator(): ArrayIterator
  {
    return new ArrayIterator($this->properties);
  }

  /**
   * Serialize all parameters
   *
   * return string
   */
  public function serialize(): string
  {
    return json_encode($this->properties);
  }

  /**
   * unserialize all parameters and return a ParameterBag
   *
   * @param $data string
   * @return ParameterBag
   */
  public function unserialize($serialized): ParameterBag
  {
    $this->properties = json_decode($serialized);
    return $this;
  }
}
