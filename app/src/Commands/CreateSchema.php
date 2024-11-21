<?php

namespace App\Commands;

use App\Database\DatabaseConnexion;
use App\Entities\AbstractEntity;
use App\Managers\AbstractManager;

class CreateSchema extends AbstractCommand {
    
    const TYPES = [
        'string' => 'VARCHAR(255)',
        'int' => 'INT',
        'float' => 'FLOAT',
        'bool' => 'BOOLEAN',
        'DateTime' => 'DATETIME'
    ];

    public function execute(): bool {
        try {

            $db = new DatabaseConnexion();
            $db->setConnexion();

            $this->createTables($this->getManagers(), $db->getConnexion());

            return true;
        } catch (\PDOException $e) {
            echo $e->getMessage();
            
            $db = null;
            return false;
        }
    }

    private function getManagers(): array {
        $managers = [];
        $files = scandir(__DIR__ . '/../Managers');
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $className = 'App\\Managers\\' . pathinfo($file, PATHINFO_FILENAME);

            if(!class_exists($className)) {
                continue;
            }

            if(!is_subclass_of($className, 'App\\Managers\\AbstractManager')) {
                continue;
            }

            if(str_ends_with($className, 'Manager') === false) {
                continue;
            }

            $managers[] = new $className();
        }

        return $managers;
    }

    private function createTables(array $managers, \PDO $db): void {
        foreach ($managers as $manager) {
            echo "Creating table for " . $manager->getTable() . PHP_EOL;
            $entity = $this->getEntityFromManager($manager);
            $typedFields = $this->getTypedFields($entity);

            $sql = "CREATE TABLE IF NOT EXISTS {$manager->getTable()} (";
            foreach ($typedFields as $key => $value) {
                $sql .= $this->getMysqlField($key, $value) . ',';
            }
            $sql = rtrim($sql, ',');

            $sql .= ");";

            $db->exec($sql);
        }
    }

    private function getEntityFromManager(AbstractManager $manager): AbstractEntity {
        $className = 'App\\Entities\\' . str_replace('Manager', '', (new \ReflectionClass($manager))->getShortName());
        return new $className();
    }

    private function getTypedFields(AbstractEntity $entity): array {
        $fields = [];

        $properties = get_class_vars(get_class($entity));
        foreach ($properties as $key => $value) {
            $rp = new \ReflectionProperty($entity, $key);
            $fields[$key] = self::TYPES[$rp->getType()->getName()];
        }

        return $fields;
    }

    private function getMysqlField(string $name, string $type): string {
        $field = "$name $type";
        if($name === 'id') {
            $field .= ' PRIMARY KEY';

            if($type === 'INT') {
                $field .= ' AUTO_INCREMENT';
            }
        }else {
            $field .= ' NOT NULL';
        }

        return $field;
    }
}