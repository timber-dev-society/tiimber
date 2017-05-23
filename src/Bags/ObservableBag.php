<?php
declare(strict_types=1);

namespace Tiimber\Bags;


use Tiimber\Bags\ParameterBag;

class ObservableBag extends ParameterBag
{
  private $observers;

  public function observe(string $key, callable $callback): int
  {
    if (!isset($this->observers->{$key})) {
      $this->observers->{$key} = [];
    }
    $this->observers->{$key}[] = $callback;

    return count($this->observers->{$key}) - 1;
  }

  public function unobserve(string $key, int $callback_id)
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
  public function set(string $key, $value): ParameterBag
  {
    if (isset($this->observers->{$key})) {
      foreach ($this->observers->{$key} as $callback) {
        $callback($this->get($key, null), $value);
      }
    }
    $this->properties->{$key} = $value;
    return $this;
  }
}
