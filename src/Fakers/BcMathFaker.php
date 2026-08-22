<?php
namespace Apie\Faker\Fakers;

use Apie\Faker\Interfaces\ApieClassFaker;
use BcMath\Number;
use Faker\Generator;
use ReflectionClass;

/** @implements ApieClassFaker<Number> */
class BcMathFaker implements ApieClassFaker
{
    public function supports(ReflectionClass $class): bool
    {
        return $class->name === Number::class;
    }

    public function fakeFor(Generator $generator, ReflectionClass $class): Number
    {
        return new Number((string) $generator->randomFloat());
    }
}
