<?php
declare(strict_types=1);

namespace Tiimber\Bags;

use ArrayIterator;
use IteratorAggregate;
use stdClass;

class ImmutableBag implements IteratorAggregate
{
  private $properties;

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
    return $this->properties->{$key} ?? $default;
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

  public function toArray(): array
  {
    return (array)$this->properties;
  }
}
