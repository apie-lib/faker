<?php
namespace Apie\Faker\Fakers;

use Apie\DateValueObjects\Concerns\CanCreateInstanceFromDateTimeObject;
use Apie\DateValueObjects\UnixTimestamp;
use Apie\Faker\Interfaces\ApieClassFaker;
use DateTime;
use Faker\Generator;
use ReflectionClass;

/** @implements ApieClassFaker<UnixTimestamp|(ValueObjectInterface&CanCreateInstanceFromDateTimeObject)> */
class DateValueObjectFaker implements ApieClassFaker
{
    public function supports(ReflectionClass $class): bool
    {
        return $class->name === UnixTimestamp::class
            || in_array(CanCreateInstanceFromDateTimeObject::class, $class->getTraitNames());
    }

    public function fakeFor(Generator $generator, ReflectionClass $class): object
    {
        $date = new DateTime('@' . $generator->unixTime());
        $className = $class->name;
        return $className::createFromDateTimeObject($date);
    }
}
