<?php
namespace Apie\Faker\Fakers;

use Apie\Core\ValueObjects\Interfaces\TimeRelatedValueObjectInterface;
use Apie\DateValueObjects\Concerns\CanCreateInstanceFromDateTimeObject;
use Apie\DateValueObjects\UnixTimestamp;
use Apie\Faker\Interfaces\ApieClassFaker;
use DateTime;
use Faker\Generator;
use ReflectionClass;

/** @implements ApieClassFaker<TimeRelatedValueObjectInterface> */
class DateValueObjectFaker implements ApieClassFaker
{
    public function supports(ReflectionClass $class): bool
    {
        return $class->implementsInterface(TimeRelatedValueObjectInterface::class);
    }

    public function fakeFor(Generator $generator, ReflectionClass $class): TimeRelatedValueObjectInterface
    {
        $date = new DateTime('@' . $generator->unixTime());
        $className = $class->name;
        return $className::createFromDateTimeObject($date);
    }
}
