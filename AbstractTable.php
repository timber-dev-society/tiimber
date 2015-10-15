<?php
namespace KissPHP;

use KissPHP\Sql;
use KissPHP\AbstractModel;

abstract class AbstractTable
{
  private static $stringTypes = ['char', 'varchar', 'text', 'timestamp', 'date', 'enum'];

  public function isNew()
  {
    return (boolean)$this->entity->id;
  }

  public function getNamespace()
  {
    return 'KissPHP\\Models\\' . static::ENTITY;
  }

  public function hydrate($values)
  {
    $classname = $this->getNamespace();
    $obj = new $classname((object)$values);

    $this->onLoad($obj);

    return $obj;
  }

  public function hydrateCollection($collection)
  {
    foreach ($collection as $key => $value) {
      $collection[$key] = $this->hydrate($value);
    }

    return $collection;
  }

  public function save()
  {
    if ($this->isNew) {
      $this->create();
    } else {
      $this->update();
    }
  }

  public function find($id)
  {
    $sql = 'SELECT ' . static::TABLE . '.* FROM ' . static::TABLE . ' WHERE ' . static::TABLE . '.id=' . $id;
    $values = $this->execute($sql)->fetch();
    return $this->hydrate($values);
  }

  public function findBy(array $where)
  {
    $sql = 'SELECT ' . static::TABLE . '.* FROM ' . static::TABLE . ' WHERE ';

    foreach($where as $column => $value) {
      $where[$column] = static::TABLE . '.' . $column . '="' . $value . '"';
    };

    $values = $this->execute($sql . implode(' AND ', $where))->fetchAll();

    foreach ($values as $key => $value) {
      $values[$key] = $this->hydrate($value);
    }

    return $values;
  }

  public function findOneBy(array $where)
  {
    return reset($this->findBy($where));
  }

  public function findAll()
  {
    $query = 'SELECT * FROM ' . static::TABLE;
    $values = $this->execute($query)->fetchAll();

    return $this->hydrateCollection($values);
  }

  public function paginate($query, $page = 0, $limite = 10)
  {

  }

  public function execute($sql)
  {
    $select = Sql::connect()->query($sql);
    $select->setFetchMode(\PDO::FETCH_OBJ);

    return $select;
  }

  public function createFromPost($request)
  {
    $values = $request->post;
    $properties = $this->execute('desc ' . static::TABLE)->fetchAll();

    $entity = new \stdClass();
    $this->parseTableDefinition($values, function ($field, $type) use ($entity, $values) {
      $entity->{$field} = $values->{$field};
    });

    return $this->create($entity);
  }

  public function create($entity)
  {
    $entity = (object)$entity;
    $this->beforeCreate($entity);
    $properties = $this->execute('desc ' . static::TABLE)->fetchAll();

    $this->parseTableDefinition($entity, function ($field, $type) use ($entity) {
      if (in_array($type, self::$stringTypes)) {
        $entity->{$field} = '"' . addslashes($entity->{$field}) . '"';
      }
    });

    $columns = implode(', ', array_keys((array)$entity));
    $values = implode(', ', (array)$entity);

    $sql = 'INSERT INTO ' . static::TABLE . '(' . $columns . ') VALUES (' . $values .')';

    $request = Sql::connect()->prepare($sql);
    $request->execute();
  }

  private function parseTableDefinition($entity, $callback)
  {
    $properties = $this->execute('desc ' . static::TABLE)->fetchAll();

    foreach ($properties as $property) {
      if (property_exists($entity, $property->Field)) {
        $callback($property->Field, reset(explode('(', $property->Type)));
      }
    }
  }

   /**
   * update() $entity, an array of data to update.
   */
  public function update($entity)
  {
    $this->beforeUpdate($entity);
    $data = [];
    if ($entity instanceof AbstractModel) {
      $entity = $entity->getEntity();
    }
    if (is_array($entity)) {
      $entity = (object)$entity;
    }

    $this->parseTableDefinition($entity, function ($field, $type) use ($entity, &$data) {
      if (in_array($type, self::$stringTypes)) {
        $data[] = $field . '="' . addslashes($entity->{$field}) . '"';
      } else {
        $data[] = $field . '=' . $entity->{$field};
      }
    });

    $data = implode(', ', $data);
    $sql = 'UPDATE ' . static::TABLE . ' SET ' . $data . ' WHERE id=' . $entity->id;

    $request = Sql::connect()->prepare($sql);
    $request->execute();
  }

  /**
   * deleteEntity() $id: remove the entity from table where $id
   */
  public function deleteEntity($id)
  {
    $sql = 'DELETE FROM ' . static::TABLE . ' WHERE id = '. $id;
    $request = Sql::connect()->prepare($sql);
    $request->execute();
  }

  public function dateNow()
  {
    return date('Y-m-d H:i:s');
  }

  public function beforeCreate($entity)
  {
  }

  public function beforeUpdate($entity)
  {
  }

  public function onLoad($entity)
  {
  }
}