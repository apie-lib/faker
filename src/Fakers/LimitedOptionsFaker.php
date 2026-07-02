<?php
namespace Apie\Faker\Fakers;

use Apie\Core\ValueObjects\Interfaces\LimitedOptionsInterface;
use Apie\Faker\Interfaces\ApieClassFaker;
use Faker\Generator;
use ReflectionClass;

/** @implements ApieClassFaker<LimitedOptionsInterface> */
class LimitedOptionsFaker implements ApieClassFaker
{
    public function supports(ReflectionClass $class): bool
    {
        if (in_array(LimitedOptionsInterface::class, $class->getInterfaceNames())) {
            $options = $class->getMethod('getOptions')->invoke(null)->toArray();
            return !empty($options);
        }

        return false;
    }

    public function fakeFor(Generator $generator, ReflectionClass $class): object
    {
        $options = $class->getMethod('getOptions')->invoke(null)->toArray();
        return $class->getMethod('fromNative')->invoke(null, $generator->randomElement($options));
    }
}
