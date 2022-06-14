<?php
namespace Apie\Faker\Fakers;

use Apie\Core\RegexUtils;
use Apie\Core\ValueObjects\Interfaces\HasRegexValueObjectInterface;
use Apie\Core\ValueObjects\Interfaces\StringValueObjectInterface;
use Apie\Faker\Interfaces\ApieClassFaker;
use Faker\Generator;
use ReflectionClass;
use RegRev\RegRev;

/** @implements ApieClassFaker<HasRegexValueObjectInterface> */
class StringValueObjectWithRegexFaker implements ApieClassFaker
{
    public function supports(ReflectionClass $class): bool
    {
        return $class->implementsInterface(HasRegexValueObjectInterface::class);
    }

    public function fakeFor(Generator $generator, ReflectionClass $class): StringValueObjectInterface&HasRegexValueObjectInterface
    {
        $className = $class->name;
        $regularExpressionWithDelimiter = $className::getRegularExpression();
        $regex = RegexUtils::removeDelimiters($regularExpressionWithDelimiter);
        return $className::fromNative(RegRev::generate($regex));
    }
}
