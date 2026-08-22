<?php
namespace Apie\Faker\Fakers;

use Apie\Faker\Interfaces\ApieClassFaker;
use Faker\Generator;
use GMP;
use ReflectionClass;

/** @implements ApieClassFaker<GMP> */
class GmpFaker implements ApieClassFaker
{
    public function supports(ReflectionClass $class): bool
    {
        return $class->name === GMP::class;
    }

    public function fakeFor(Generator $generator, ReflectionClass $class): GMP
    {
        $value = (string) $generator->randomNumber(5, true);

        return new GMP($value, 10);
    }
}
