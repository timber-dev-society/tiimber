<?php
declare(strict_types=1);

namespace Tiimber\Bags;

use Evenement\EventEmitterTrait;

use Tiimber\ImmutableBag;

class ObservableBag extends ImmutableBag
{
  use EventEmitterTrait;

  private $properties;

  public function __construct($properties = null)
  {
    $this->bag = is_null($properties) ? new stdClass() : (object)$properties;
  }
}
