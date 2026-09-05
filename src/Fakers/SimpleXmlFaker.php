<?php
namespace Apie\Faker\Fakers;

use Apie\Faker\Interfaces\ApieClassFaker;
use Faker\Generator;
use ReflectionClass;
use SimpleXMLElement;

/** @implements ApieClassFaker<SimpleXMLElement> */
class SimpleXmlFaker implements ApieClassFaker
{
    public function supports(ReflectionClass $class): bool
    {
        return $class->name === SimpleXMLElement::class;
    }

    public function fakeFor(Generator $generator, ReflectionClass $class): object
    {
        return new SimpleXMLElement('<root />');
    }
}
