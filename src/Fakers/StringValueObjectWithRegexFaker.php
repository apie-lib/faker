<?php
namespace Apie\Faker\Fakers;

use Apie\Core\ValueObjects\IsStringWithRegexValueObject;
use Apie\Faker\Interfaces\ApieClassFaker;
use Apie\Faker\Utils\RegexUtils;
use Faker\Generator;
use ReflectionClass;
use RegRev\RegRev;

/** @implements ApieClassFaker<ValueObjectInterface&Stringable> */
class StringValueObjectWithRegexFaker implements ApieClassFaker
{
    public function supports(ReflectionClass $class): bool
    {
        return in_array(IsStringWithRegexValueObject::class, $class->getTraitNames());
    }

    public function fakeFor(Generator $generator, ReflectionClass $class): object
    {
        $className = $class->name;
        $regularExpressionWithDelimiter = $className::getRegularExpression();
        $regex = RegexUtils::removeDelimiters($regularExpressionWithDelimiter);
        return $className::fromNative(RegRev::generate($regex));
    }
}
