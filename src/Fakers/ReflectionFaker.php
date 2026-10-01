<?php
namespace Apie\Faker\Fakers;

use Apie\Faker\Interfaces\ApieClassFaker;
use Faker\Generator;
use ReflectionClass;
use ReflectionMethod;

/** @implements ApieClassFaker<ReflectionClass<covariant object>|ReflectionMethod> */
class ReflectionFaker implements ApieClassFaker
{
    public function supports(ReflectionClass $class): bool
    {
        return $class->name === ReflectionClass::class || $class->name === ReflectionMethod::class;
    }

    public function fakeFor(Generator $generator, ReflectionClass $class): ReflectionClass|ReflectionMethod
    {
        if ($class->name === ReflectionMethod::class) {
            return ReflectionMethod::createFromMethodName(__METHOD__);
        }
        return new ReflectionClass(__CLASS__);
    }
}
