<?php
namespace Apie\Faker\Fakers;

use Apie\Faker\Interfaces\ApieClassFaker;
use Faker\Generator;
use ReflectionClass;
use Uri\Rfc3986\Uri;

/** @implements ApieClassFaker<Uri> */
class UriFaker implements ApieClassFaker
{
    public function supports(ReflectionClass $class): bool
    {
        return $class->name === Uri::class;
    }

    public function fakeFor(Generator $generator, ReflectionClass $class): Uri
    {
        return new Uri($generator->url());
    }
}
