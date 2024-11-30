<?php

namespace App\Managers;

abstract class AbstractManager
{
    protected string $table;
    public function find($id): AbstractEntity
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function findOneBy(array $criterias): array
    {
        $connexion = $this->dbConnection();

        $this->select();
        $this->from();

        foreach ($criterias as $key => $value) {
            if (strpos($this->query, 'WHERE') === false) {
                $this->where($key, '=');
            } else {
                $this->and($key, '=');
            }
        }

        $connexion->prepare($this->query);
        $connexion->execute([$criterias]);
        $data = $connexion->fetch();
        return $data;
    }

    public function findAll(): array
    {
        return $this->findBy([]);
    }

    public function findBy(array $criterias): AbstractEntity
    {
        $connexion = $this->dbConnection();
        $query = "SELECT * FROM {$this->table}";

        foreach ($criterias as $key => $value) {
            if (strpos($query, 'WHERE') === false) {
                $query .= "WHERE";
            } else {
                $query .= " AND";
            }

            $query .= " $key = :$";
        }

        $connexion->prepare($query);
        $connexion->execute();
        $data = $connexion->fetchAll();
        return $data;
    }

    public function select()
    {
        $this->query = 'SELECT';
    }

    public function fields(array $fields)
    {
        if (empty($fields)) {
            $this->query .= ' *';
        }

        $fieldsAsString = implode(', ', $fields);
        $this->query .= $fieldsAsString;
    }

    public function from()
    {
        $this->query .= " FROM $this->table";
    }

    public function where(string $field, string $operator)
    {
        $this->query .= " WHERE $field $operator :$field";
    }

    public function and(string $field, string $operator)
    {
        $this->query .= " AND";
        $this->where($field, $operator);
    }

    public function or(string $field, string $operator)
    {
        $this->query .= " OR";
        $this->where($field, $operator);
    }
}
