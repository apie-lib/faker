<?php
namespace Apie\Faker\Fakers;

use Apie\Faker\Interfaces\ApieClassFaker;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use DateTimeInterface;
use Faker\Generator;
use ReflectionClass;

/** @implements ApieClassFaker<DatePeriod<DateTimeInterface, DateTimeInterface|null, int|null>> */
class DatePeriodFaker implements ApieClassFaker
{
    public function supports(ReflectionClass $class): bool
    {
        return $class->name === DatePeriod::class;
    }

    /**
     * @return DatePeriod<DateTimeImmutable, DateTimeInterface|null, int|null>
     */
    public function fakeFor(Generator $generator, ReflectionClass $class): DatePeriod
    {
        $options = ($generator->boolean() ? DatePeriod::EXCLUDE_START_DATE : 0)
            | ($generator->boolean() ? DatePeriod::INCLUDE_END_DATE : 0);
        return new DatePeriod(
            $generator->fakeClass(DateTimeImmutable::class),
            $generator->fakeClass(DateInterval::class),
            $generator->boolean() ? $generator->fakeClass(DateTimeImmutable::class) : $generator->numberBetween(1, 10),
            $options
        );
    }
}
