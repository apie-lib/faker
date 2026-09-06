<?php
namespace Apie\Faker\Fakers;

use Apie\Faker\Interfaces\ApieClassFaker;
use DateInterval;
use Faker\Generator;
use ReflectionClass;

/** @implements ApieClassFaker<DateInterval> */
class DateIntervalFaker implements ApieClassFaker
{
    public function supports(ReflectionClass $class): bool
    {
        return $class->name === DateInterval::class;
    }

    public function fakeFor(Generator $generator, ReflectionClass $class): DateInterval
    {
        $interval = sprintf(
            'P%dY%dM%dDT%dH%dM%dS',
            $generator->numberBetween(0, 100),
            $generator->numberBetween(0, 11),
            $generator->numberBetween(0, 30),
            $generator->numberBetween(0, 23),
            $generator->numberBetween(0, 59),
            $generator->numberBetween(0, 59)
        );

        return new DateInterval($interval);
    }
}
