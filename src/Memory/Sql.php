<?php
namespace Tiimber\Memory;

use Spot\Entity;

use Tiimber\Database;

class Sql extends Entity
{
  protected static $table = 'tiimber_memory';
  
  public static function fields()
  {
    return [
      'scope' => ['type' => 'string', 'required' => true, 'primary' => true],
      'values' => ['type' => 'text', 'required' => true]
    ];
  }

  /**
   * store the current state of scope values.
   *
   * @param $scope string
   * @param $value ParameterBag
   * @return void
   */
  public function store(string $scope, ParameterBag $values)
  {
    $mapper = Database::connect()->mapper('Tiimber\Memory\Sql');
    $entity = $mapper->build([
      'scope' => $scope,
      'values' => $values->serialize()
    ]);
    $mapper->save($entity);
  }

  /**
   * restore the previus state of scope values.
   *
   * @param $scope string
   * @return ParameterBag
   */
  public function restore(string $scope): ParameterBag
  {
    $mapper = Database::connect()->mapper('Tiimber\Memory\Sql');
    $entity = $mapper->get($scope);
    return (new ParameterBag())->unserialize($entity->values);
  }
}
