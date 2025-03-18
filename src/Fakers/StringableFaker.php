<?php
namespace Apie\Faker\Fakers;

use Apie\Core\ValueObjects\BinaryStream;
use Apie\Faker\Interfaces\ApieClassFaker;
use Faker\Generator;
use ReflectionClass;
use Stringable;

/** @implements ApieClassFaker<Stringable> */
class StringableFaker implements ApieClassFaker
{
    public function supports(ReflectionClass $class): bool
    {
        return $class->name === Stringable::class;
    }

    public function fakeFor(Generator $generator, ReflectionClass $class): object
    {
        return new BinaryStream($generator->text());
    }
}
