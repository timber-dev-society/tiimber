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

  public function __construct(array $properties = null)
  {
    if ($properties !== null) {
      foreach($properties as $property) {
        $this->checkProperty($property);
      }
    }
    
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
    return $this->properties->{$key} ?? $default;
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
    $this->checkProperty($value);
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

  public function unset(string $key): ParameterBag
  {
    if ($this->has($key)) {
      unset($this->properties->{$key});
    }
    return $this;
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

  private function checkProperty($property)
  {
    if (is_object($property) && !$property instanceof Serializable) {
      throw new Exception('To store ' . get_class($property) . 'in a ParameterBag, your object must be implementing Serializable interface.', 500);
    }
  }
}
