<?php
namespace Apie\Faker\Fakers;

use Apie\Core\Randomizer\RandomizerFromFaker;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use Apie\Core\ValueObjects\IsPasswordValueObject;
use Apie\Faker\Interfaces\ApieClassFaker;
use Faker\Generator;
use ReflectionClass;

/** @implements ApieClassFaker<ValueObjectInterface> */
class PasswordValueObjectFaker implements ApieClassFaker
{
    public function supports(ReflectionClass $class): bool
    {
        return $class->implementsInterface(ValueObjectInterface::class) && in_array(IsPasswordValueObject::class, $class->getTraitNames());
    }

    public function fakeFor(Generator $generator, ReflectionClass $class): ValueObjectInterface
    {
        $className = $class->name;
        return $className::createRandom(new RandomizerFromFaker($generator));
    }
}
