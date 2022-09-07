<?php
namespace Apie\Faker\Fakers;

use Apie\Faker\Interfaces\ApieClassFaker;
use Faker\Generator;
use ReflectionClass;
use UnitEnum;

/** @implements ApieClassFaker<UnitEnum> */
class EnumFaker implements ApieClassFaker
{
    public function supports(ReflectionClass $class): bool
    {
        return $class->implementsInterface(UnitEnum::class);
    }

    public function fakeFor(Generator $generator, ReflectionClass $class): object
    {
        $className = $class->name;
        return $generator->randomElement($className::cases());
    }
}
