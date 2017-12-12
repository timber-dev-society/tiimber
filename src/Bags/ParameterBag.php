<?php
declare(strict_types=1);
namespace Tiimber\Bags;

use ArrayIterator;
use IteratorAggregate;
use Serializable;
use stdClass;

use Tiimber\Exception;

class ParameterBag implements IteratorAggregate
{
  protected $properties;

  public function __construct(array $properties = null)
  {
    $this->properties = $properties ?? [];
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
    return $this->properties[$key] ?? $default;
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
    $this->properties[$key] = $value;
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
    return isset($this->properties[$key]);
  }

  public function unset(string $key): ParameterBag
  {
    if ($this->has($key)) {
      unset($this->properties[$key]);
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

  public function __GET(string $key)
  {
    return $this->get($key);
  }

  public function merge(array $values): ParameterBag
  {
    $properties = array_merge($this->properties, $values);
    return new ParameterBag($properties);
  }

  public function toArray(): array
  {
    return $this->properties;
  }
}
