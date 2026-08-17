<?php
namespace Apie\Faker\Fakers;

use Apie\Faker\Interfaces\ApieClassFaker;
use Faker\Generator;
use ReflectionClass;
use Time\Duration;

/** @implements ApieClassFaker<Duration> */
class DurationFaker implements ApieClassFaker
{
    public function supports(ReflectionClass $class): bool
    {
        return $class->name === Duration::class;
    }

    public function fakeFor(Generator $generator, ReflectionClass $class): Duration
    {
        return Duration::fromSeconds($generator->numberBetween(0, 1000), $generator->numberBetween(0, 999999999));
    }
}
