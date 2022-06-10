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
     * @param ReflectionClass<object>
     */
    public function supports(ReflectionClass $class): bool;
    /**
     * @param ReflectionClass<T>
     * @return T
     */
    public function fakeFor(Generator $generator, ReflectionClass $class): object;
}
