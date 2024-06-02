<?php
namespace Apie\Faker\Fakers;

use Apie\Faker\Interfaces\ApieClassFaker;
use DateTime;
use DateTimeInterface;
use Faker\Generator;
use ReflectionClass;

/** @implements ApieClassFaker<DateTimeInterface> */
class PhpDateTimeObjectFaker implements ApieClassFaker
{
    public function supports(ReflectionClass $class): bool
    {
        return $class->implementsInterface(DateTimeInterface::class);
    }

    public function fakeFor(Generator $generator, ReflectionClass $class): DateTimeInterface
    {
        $className = $class->name;
        return match ($className) {
            DateTimeInterface::class => new DateTime('@' . $generator->unixTime()),
            default => new $className('@' . $generator->unixTime()),
        };
    }
}
