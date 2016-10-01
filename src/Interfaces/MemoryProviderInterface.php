<?php
declare(strict_types=1);
namespace Tiimber\Interfaces;

use Tiimber\ParameterBag;

interface MemoryProviderInterface
{
  /**
   * store the current state of scope values.
   *
   * @param $scope string
   * @param $value ParameterBag
   * @return void
   */
  public function store(string $scope, ParameterBag $values);

  /**
   * restore the previus state of scope values.
   *
   * @param $scope string
   * @return ParameterBag
   */
  public function restore(string $scope): ParameterBag;
}
