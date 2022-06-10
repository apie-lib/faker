<?php
namespace Apie\Faker\Fakers;

use Apie\CommonValueObjects\Names\FirstName;
use Apie\CommonValueObjects\Names\LastName;
use Apie\Faker\Interfaces\ApieClassFaker;
use Faker\Generator;
use ReflectionClass;
use UnitEnum;

/** @implements ApieClassFaker<UnitEnum> */
class UseDefaultGeneratorFaker implements ApieClassFaker
{
    private const MAPPING = [
        FirstName::class => 'firstName',
        LastName::class => 'lastName',
    ];

    public function supports(ReflectionClass $class): bool
    {
        return isset(self::MAPPING[$class->name]);
    }

    public function fakeFor(Generator $generator, ReflectionClass $class): object
    {
        $className = $class->name;
        $methodName = self::MAPPING[$class->name];
        return $className::fromNative($generator->$methodName());
    }
}
