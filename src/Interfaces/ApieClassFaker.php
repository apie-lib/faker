<?php
namespace Apie\Faker\Interfaces;

use Faker\Generator;
use ReflectionClass;

/**
 * @template T of object
 */
interface ApieClassFaker
{
    /**
     * @param ReflectionClass<object> $class
     */
    public function supports(ReflectionClass $class): bool;
    /**
     * @param ReflectionClass<T> $class
     * @return T
     */
    public function fakeFor(Generator $generator, ReflectionClass $class): mixed;
}
