<?php
declare(strict_types=1);
namespace Tiimber\Bags;

use ArrayIterator;
use IteratorAggregate;
use Serializable;
use stdClass;

use Tiimber\Exception;
use Tiimber\Bags\ParameterBag;

class SerializableBag extends ParameterBag implements IteratorAggregate, Serializable
{
  public function __construct($properties = null)
  {
    if ($properties !== null) {
      foreach($properties as $property) {
        $this->checkProperty($property);
      }
    }

    parent::__construct($properties);
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
   * Serialize all parameters
   *
   * return string
   */
  public function serialize(): string
  {
    return serialize($this->properties);
  }

  /**
   * unserialize all parameters and return a ParameterBag
   *
   * @param $data string
   * @return ParameterBag
   */
  public function unserialize($serialized): SerializableBag
  {
    $properties = unserialize($serialized);
    $this->properties = is_null($properties) ? new stdClass() : (object)$properties;

    return $this;
  }

  private function checkProperty($property)
  {
    if (is_object($property) && !$property instanceof Serializable) {
      throw new Exception('To store ' . get_class($property) . 'in a SerializableBag, your object must be implementing Serializable interface.', 500);
    }
  }
}
