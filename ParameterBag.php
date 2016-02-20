<?php
namespace KissPHP;

class ParameterBag extends AbstractModel implements \IteratorAggregate
{
  public function get($key, $default = null)
  {
    if (isset($this->{$key})) {
      return $this->{$key};
    }
    return $default;
  }

  public function set($key, $value)
  {
    $this->{$key} = $value;
  }

  public function getIterator()
  {
    return $this->properties;
  }
}