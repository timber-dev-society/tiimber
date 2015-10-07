<?php
namespace KissPHP;

use KissPHP\Sql;

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
    $sql = 'SELECT * FROM ' . static::TABLE;
    $values = $this->execute($sql)->fetchAll();

    foreach ($values as $key => $value) {
      $values[$key] = $this->hydrate($value);
    }

    return $values;
  }

  public function execute($sql)
  {
    $connection = Sql::getInstance();

    $select = $connection->query($sql);
    $select->setFetchMode(\PDO::FETCH_OBJ);
    return $select;
  }

  public function createFromPost($request)
  {
    $values = $request->post;
    $properties = $this->execute('desc ' . static::TABLE)->fetchAll();

    $entity = new \stdClass();
    foreach ($properties as $property) {
      if (property_exists($values, $property->Field)) {
        $entity->{$property->Field} = $values->{$property->Field};
      }
    }

    return $this->create($entity);
  }

  public function create($entity)
  {
    $this->beforeCreate($entity);
    $properties = $this->execute('desc ' . static::TABLE)->fetchAll();

    foreach ($properties as $property) {
      if (property_exists($entity, $property->Field)) {
        $type = reset(explode('(', $property->Type));

        if (in_array($type, self::$stringTypes)) {
          $entity->{$property->Field} = '"' . addslashes($entity->{$property->Field}) . '"';
        }
      }
    }
    $columns = implode(', ', array_keys((array)$entity));
    $values = implode(', ', (array)$entity);

    $sql = 'INSERT INTO ' . static::TABLE . '(' . $columns . ') VALUES (' . $values .')';

    $connection = Sql::getInstance();
    $request = $connection->prepare($sql);
    $request->execute();
  }

  public function update($entity)
  {
    $this->beforeUpdate($entity);
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