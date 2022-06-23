<?php
namespace Apie\Faker\Fakers;

use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use Apie\Core\ValueObjects\IsPasswordValueObject;
use Apie\Faker\Interfaces\ApieClassFaker;
use Faker\Generator;
use ReflectionClass;

/** @implements ApieClassFaker<ValueObjectInterface|IsPasswordValueObject> */
class PasswordValueObjectFaker implements ApieClassFaker
{
    public function supports(ReflectionClass $class): bool
    {
        return $class->implementsInterface(ValueObjectInterface::class) && in_array(IsPasswordValueObject::class, $class->getTraitNames());
    }

    public function fakeFor(Generator $generator, ReflectionClass $class): ValueObjectInterface
    {
        $className = $class->name;
        $minLength = $className::getMinLength();
        $maxLength = $className::getMaxLength();
        $minSpecialCharacters = $className::getMinSpecialCharacters();
        $minDigits = $className::getMinDigits();
        $minLowercase = $className::getMinLowercase();
        $minUppercase = $className::getMinUppercase();
        $specialCharacters = str_split($className::getAllowedSpecialCharacters());
        $generatedPassword = implode('', $generator->randomElements($specialCharacters, $minSpecialCharacters));
        for ($i = 0; $i < $minDigits; $i++) {
            $generatedPassword .= $generator->randomDigit();
        }
        for ($i = 0; $i < $minLowercase; $i++) {
            $generatedPassword .= $generator->randomElement(['a', 'b', 'c', 'd', 'e', 'f']);
        }
        for ($i = 0; $i < $minUppercase; $i++) {
            $generatedPassword .= $generator->randomElement(['A', 'B', 'C', 'D', 'E', 'F']);
        }
        for ($i = strlen($generatedPassword); $i < $minLength; $i++) {
            $generatedPassword .= $generator->randomElement(['g', 'G', 'h', 'H']);
        }
        if (strlen($generatedPassword) > $maxLength) {
            $generatedPassword = substr(0, $maxLength);
        }

        return $className::fromNative($generatedPassword);
    }
}
