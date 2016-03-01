<?php
namespace KissPHP;

use ArrayIterator;
use IteratorAggregate;

class ParameterBag extends AbstractModel implements IteratorAggregate
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
}