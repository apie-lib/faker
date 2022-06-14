<?php
namespace Apie\Faker\Fakers;

use Apie\CommonValueObjects\Text\NonEmptyString;
use Apie\CommonValueObjects\Text\SmallDatabaseText;
use Apie\CommonValueObjects\Texts\DatabaseText;
use Apie\Faker\Interfaces\ApieClassFaker;
use Faker\Generator;
use ReflectionClass;

/** @implements ApieClassFaker<DatabaseText|SmallDatabaseText|NonEmptyString> */
class TextValueObjectFaker implements ApieClassFaker
{
    public function supports(ReflectionClass $class): bool
    {
        return in_array(
            $class->name,
            [
                DatabaseText::class,
                SmallDatabaseText::class,
                NonEmptyString::class,
            ]
        );
    }

    protected function maximumLength(string $className): int
    {
        if ($className === SmallDatabaseText::class) {
            return 80;
        }
        return 1024;
    }

    public function fakeFor(Generator $generator, ReflectionClass $class): object
    {
        $className = $class->name;
        return $className::fromNative(
            $generator->realText(
                $this->maximumLength($className)
            )
        );
    }
}
