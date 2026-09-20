<?php
namespace Apie\Faker\Fakers;

use Apie\Core\Attributes\ConcreteClasses;
use Apie\Faker\Interfaces\ApieClassFaker;
use Faker\Generator;
use ReflectionClass;

/** @implements ApieClassFaker<object> */
class InterfaceFaker implements ApieClassFaker
{
    public function supports(ReflectionClass $class): bool
    {
        return !empty($class->getAttributes(ConcreteClasses::class));
    }

    public function fakeFor(Generator $generator, ReflectionClass $class): object
    {
        $randomAttribute = $generator->randomElement($class->getAttributes(ConcreteClasses::class));
        $randomClass = $generator->randomElement($randomAttribute->newInstance()->classes);
        return $generator->fakeClass($randomClass);
    }
}
