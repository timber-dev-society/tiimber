<?php
namespace Tiimber\Bags;

use ArrayIterator;
use IteratorAggregate;
use stdClass;

use Tiimber\{View, Bags\ParameterBag};

class RenderableBag extends ParameterBag implements IteratorAggregate
{
  /**
   * Serialize all parameters
   *
   * return string
   */
  public function each(callable $callback): string
  {
    $return = '';
    foreach ($this->properties as $key => $value) {
      $val = $callback($value, $key);
      if ($val instanceof View) {

      } else {
        $return .= $val;
      }
    }
    return $return;
  }

  public function render($tpl): callable
  {
    return function ($value, $key) use ($tpl) {

    };
  }
}
