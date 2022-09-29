<?php
namespace Apie\Faker\Fakers;

use Apie\Faker\Interfaces\ApieClassFaker;
use Faker\Generator;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/** @implements ApieClassFaker<object> */
class UseConstructorFaker implements ApieClassFaker
{
    public function supports(ReflectionClass $class): bool
    {
        return is_null($class->getConstructor()) || $class->getConstructor()->isPublic();
    }

    public function fakeFor(Generator $generator, ReflectionClass $class): object
    {
        $constructor = $class->getConstructor();
        $arguments = $constructor ? $generator->fakeArgumentsOfMethod($constructor) : [];
        $object = $class->newInstance(...$arguments);
        foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($generator->randomElement([0, 1]) && preg_match('/^(set|with)([A-Z].*)$/', $method->name)) {
                $arguments = $generator->fakeArgumentsOfMethod($method);
                $result = $method->invoke($object, ...$arguments);
                // in case object is immutable or polymorphic...
                if (is_object($result) && $class->isInstance($result)) {
                    $object = $result;
                }
            }
        }
        foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if (!$property->isReadOnly() || !$property->isInitialized($object)) {
                $object->{$property->name} = $generator->fakeFromType($property->getType());
            }
        }
        return $object;
    }
}
