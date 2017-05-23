<?php
namespace Tiimber\Interfaces;

use Tiimber\Bags\ImmutableBag;

interface ReducerInterface
{
  public function onAction($state, $action);

  public function __invock($state, $action);
}