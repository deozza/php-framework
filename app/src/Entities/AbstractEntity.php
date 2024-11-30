<?php

abstract class AbstractEntity
{
    abstract protected function getId();

    public function toArray(): array
    {
        $array = [];
        foreach ($this as $key => $value) {
            $array[$key] = $value;
        }
        return $array;
    }
}
