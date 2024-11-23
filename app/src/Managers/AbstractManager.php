<?php

namespace App\Managers;

use App\Database\DatabaseConnexion;
use App\Database\Dsn;
use App\Entities\AbstractEntity;

abstract class AbstractManager
{
    protected DatabaseConnexion $db;

    public function __construct()
    {
        $dsn = new Dsn();
        $db = new DatabaseConnexion();
        $db->setConnexion($dsn);
        $this->db = $db;
    }

    public function getTable(): string {
        return str_replace('manager','',strtolower((new \ReflectionClass($this))->getShortName()));
    }

    private function getFields(AbstractEntity $entity): string {
        $fields = [];
        foreach ($entity->toArray() as $key => $value) {
            $fields[] = $key;
        }

        return implode(', ', $fields);
    }

    private function getValues(AbstractEntity $entity): string {
        $values = [];
        foreach ($entity->toArray() as $key => $value) {
            $values[] = ':' . $key;
        }

        return implode(', ', $values);
    }

    private function getWheres(array $criteria): string {
        $wheres = [];
        foreach ($criteria as $key => $value) {
            $wheres[] = $key . ' = :' . $key;
        }

        return implode(' AND ', $wheres);
    }

    public function find(string | int $id) {
        return $this->findOneBy(['id' => $id]);
    }

    public function findAll() {
        $query = $this->db->getConnexion()->prepare("SELECT * FROM {$this->getTable()}");
        $query->execute();
        $query->setFetchMode(\PDO::FETCH_CLASS, 'App\Entities\\' . ucfirst($this->getTable()));
        return $query->fetchAll();
    }

    public function findBy(array $criteria) {
        $query = $this->db->getConnexion()->prepare("SELECT * FROM {$this->getTable()} WHERE {$this->getWheres($criteria)}");
        $query->execute($criteria);
        $query->setFetchMode(\PDO::FETCH_CLASS, 'App\Entities\\' . ucfirst($this->getTable()));
        return $query->fetchAll();
    }

    public function findOneBy(array $criteria) {
        $query = $this->db->getConnexion()->prepare("SELECT * FROM {$this->getTable()} WHERE {$this->getWheres($criteria)}");
        $query->execute($criteria);
        $query->setFetchMode(\PDO::FETCH_CLASS, 'App\Entities\\' . ucfirst($this->getTable()));
        $data = $query->fetch();

        if($data === false) {
            return null;
        }

        return $data;
    }

    public function save(AbstractEntity $entity): string {
        $query = $this->db->getConnexion()->prepare("INSERT INTO {$this->getTable()} ({$this->getFields($entity)}) VALUES ({$this->getValues($entity)})");
        $query->execute($entity->toArray());
        return $this->db->getConnexion()->lastInsertId();
    }

    public function update(AbstractEntity $entity) {
        $query = $this->db->getConnexion()->prepare("UPDATE {$this->getTable()} SET {$this->getUpdateFields($entity)} WHERE id = :id");
        $query->execute(array_merge($entity, ['id' => $entity->getId()]));
    }

    public function delete(AbstractEntity $entity) {
        $query = $this->db->getConnexion()->prepare("DELETE FROM {$this->getTable()} WHERE id = :id");
        $query->execute(['id' => $entity->getId()]);
    }
}