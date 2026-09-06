<?php
namespace Apie\Faker\Fakers;

use Apie\Faker\Interfaces\ApieClassFaker;
use DateTimeZone;
use Faker\Generator;
use ReflectionClass;

/** @implements ApieClassFaker<DateTimeZone> */
class DateTimeZoneFaker implements ApieClassFaker
{
    public function supports(ReflectionClass $class): bool
    {
        return $class->name === DateTimeZone::class;
    }

    public function fakeFor(Generator $generator, ReflectionClass $class): DateTimeZone
    {
        return new DateTimeZone($generator->randomElement(DateTimeZone::listIdentifiers()));
    }
}
