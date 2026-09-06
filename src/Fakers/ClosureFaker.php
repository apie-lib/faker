<?php
namespace Apie\Faker\Fakers;

use Apie\Faker\Interfaces\ApieClassFaker;
use Faker\Generator;
use ReflectionClass;

/** @implements ApieClassFaker<\Closure> */
class ClosureFaker implements ApieClassFaker
{
    public function supports(ReflectionClass $class): bool
    {
        return $class->name === \Closure::class;
    }

    public function fakeFor(Generator $generator, ReflectionClass $class): \Closure
    {
        return function () {
        };
    }
}
