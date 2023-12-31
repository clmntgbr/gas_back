<?php

namespace App\Entity\Traits;

trait ListTrait
{
    /**
     * @return array<mixed>
     */
    public static function getConstantsList(): array
    {
        $class = new \ReflectionClass(__CLASS__);
        $constants = $class->getConstants();
        $result = [];

        foreach ($constants as $constant) {
            $result[strtoupper($constant)] = $constant;
        }

        return $result;
    }
}
