<?php
namespace Tiimber;

use Tiimber\Sql;
use Tiimber\SqlException;
use Tiimber\AbstractModel;
use Tiimber\ParameterBag;

abstract class AbstractTable
{
  private static $stringTypes = ['char', 'varchar', 'text', 'timestamp', 'date', 'enum', 'datetime'];

  public function isNew()
  {
    return (boolean)$this->entity->id;
  }

  public function getNamespace()
  {
    return '\\' . static::ENTITY;
  }

  public function hydrate($values)
  {
    $classname = $this->getNamespace();
    $this->onLoad($values);
    $obj = new $classname((object)$values);

    return $obj;
  }

  public function hydrateCollection($collection)
  {
    foreach ((array)$collection as $key => $value) {
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
    $sql = 'SELECT ' . static::TABLE . '.* FROM ' . static::TABLE . ' WHERE ' . static::TABLE . '.id="' . $id . '"';
    $values = $this->execute($sql)->fetch();
    if ($values) {
      $values = $this->hydrate($values);
    }
    return $values;
  }

  public function findBy(array $where)
  {
    $sql = 'SELECT ' . static::TABLE . '.* FROM ' . static::TABLE . ' WHERE ';

    foreach($where as $column => $value) {
      $where[$column] = static::TABLE . '.' . $column . '="' . $value . '"';
    };
    $values = $this->execute($sql . implode(' AND ', $where))->fetchAll();
    foreach ($values as $key => $value) {
      if ($value) {
        $values[$key] = $this->hydrate($value);
      }
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

  public function findLast()
  {
    $query = 'SELECT * FROM ' . static::TABLE . ' ORDER BY id';
    $values = $this->execute($query)->fetch();

    return $this->hydrate($values);
  }

  public function paginate($query, $page = 1, $limite = 10)
  {
    $page = $page == 0 ? 0 : $page - 1;
    if ($query == null) {
      $query = 'SELECT * FROM ' . static::TABLE;
    }

    $query .= ' LIMIT ' . $limite;
    if ($page != 0) {
      $query .= ' OFFSET ' . ($page * $limite);
    }
    $values = $this->execute($query)->fetchAll();
    return $this->hydrateCollection($values);
  }

  public function execute($sql)
  {
    try {
      $select = Sql::connect()->query($sql);
      $select->setFetchMode(\PDO::FETCH_OBJ);
    } catch (\Exception $e) {
      throw new SqlException($e->getMessage(), $sql);
    }

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

    $sql = 'INSERT INTO ' . static::TABLE . ' (' . $columns . ') VALUES (' . $values .')';

    try {
      $request = Sql::connect()->prepare($sql);
      $request->execute();
    } catch (\Exception $e) {
      throw new SqlException($e->getMessage(), $sql);
    }

    return $this;
  }

   /**
   * update() $entity, an array of data to update.
   */
  public function update($entity)
  {
    $data = [];

    if ($entity instanceof AbstractModel) {
      $entity = $entity->getEntity();
    }
    if (is_array($entity)) {
      $entity = (object)$entity;
    }
    $this->beforeUpdate($entity);
    $id = $entity->id;
    unset($entity->id);

    $this->parseTableDefinition($entity, function ($field, $type) use ($entity, &$data) {
      if ($entity->{$field} != '' && !is_null($entity->{$field}) && $entity->{$field} != 'N;') {
        if (in_array($type, self::$stringTypes)) {
          $data[] = $field . '="' . addslashes($entity->{$field}) . '"';
        } else {
          $data[] = $field . '=' . $entity->{$field};
        }
      }
    });

    if (count($data) === 0) {
      return;
    }

    $data = implode(', ', $data);
    $sql = 'UPDATE ' . static::TABLE . ' SET ' . $data . ' WHERE id="' . $id .'"';

    try {
      $request = Sql::connect()->prepare($sql);
      $request->execute();
    } catch (\Exception $e) {
      throw new SqlException($e->getMessage(), $sql);
    }
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

  private function parseTableDefinition($entity, $callback)
  {
    $properties = $this->execute('desc ' . static::TABLE)->fetchAll();

    foreach ($properties as $property) {
      if (property_exists($entity, $property->Field)) {
        $callback($property->Field, reset(explode('(', $property->Type)));
      }
    }
  }
}