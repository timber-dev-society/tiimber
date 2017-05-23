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
  public function __construct(array $properties = null)
  {
    if ($properties !== null) {
      foreach($properties as $property) {
        $this->checkProperty($property);
      }
    }
    
    parent::__construct($properties);
  }

  /**
   * Serialize all parameters
   *
   * return string
   */
  public function serialize(): string
  {
    return json_encode($this->getProperties());
  }

  /**
   * unserialize all parameters and return a ParameterBag
   *
   * @param $data string
   * @return ParameterBag
   */
  public function unserialize($serialized): SerializableBag
  {
    $properties = json_decode($serialized);
    return new self($properties);
  }

  private function checkProperty($property)
  {
    if (is_object($property) && !$property instanceof Serializable) {
      throw new Exception('To store ' . get_class($property) . 'in a ParameterBag, your object must be implementing Serializable interface.', 500);
    }
  }
}
