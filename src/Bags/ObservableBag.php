<?php
declare(strict_types=1);

namespace Tiimber\Bags;

use Tiimber\Bags\ParameterBag;

class ObservableBag
{
  private $observers;

  private $properties;

  public function __construct(?array $properties = [])
  {
    $this->properties = new ParameterBag($properties);
  }

  public function observe(string $key, callable $callback): int
  {
    if (!isset($this->observers->{$key})) {
      $this->observers->{$key} = [];
    }
    $this->observers->{$key}[] = $callback;

    return count($this->observers->{$key}) - 1;
  }

  public function unobserve(string $key, int $callback_id): void
  {
    if (isset($this->observers->{$key}) && isset($this->observers->{$key}[$callback_id])) {
      unset($this->observers->{$key}[$callback_id]);
    }
  }

  /**
   * Set object parameter
   *
   * @param $key String
   * @param $value mixed
   * @return ParameterBag
   */
  public function set(string $key, $value): ObservableBag
  {
    if (isset($this->observers->{$key})) {
      foreach ($this->observers->{$key} as $callback) {
        $callback($this->properties->get($key, null), $value);
      }
    }
    $this->properties->set($key, $value);
    return $this;
  }

/**
 * Set object parameter
 *
 * @param $key String
 * @param $value mixed
 * @return ParameterBag
 */
public function get(string $key)
{
  if (isset($this->observers->{$key})) {
    foreach ($this->observers->{$key} as $callback) {
      $callback($this->properties->get($key, null));
    }
  }
  return $this->properties->get($key, null);
}
}
