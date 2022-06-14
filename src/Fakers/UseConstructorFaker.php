<?php
namespace Apie\Faker\Fakers;

use Apie\Faker\Interfaces\ApieClassFaker;
use Faker\Generator;
use ReflectionClass;

/** @implements ApieClassFaker<object> */
class UseConstructorFaker implements ApieClassFaker
{
    public function supports(ReflectionClass $class): bool
    {
        return !is_null($class->getConstructor());
    }
    public function fakeFor(Generator $generator, ReflectionClass $class): object
    {
        $constructor = $class->getConstructor();
        $arguments = [];
        foreach ($constructor->getParameters() as $parameter) {
            $type = $parameter->getType();
            if ($parameter->isVariadic()) {
                $rand = $generator->rand(0, 4);
                for ($i = 0; $i < $rand; $i++) {
                    $arguments[] = $generator->fakeFromType($type);
                }
            } elseif ($parameter->allowsNull() && 1 === $generator->rand(0, 4)) {
                $arguments[] = null;
            } else {
                $arguments[] = $generator->fakeFromType($type);
            }
        }
        return $class->newInstance(...$arguments);
    }
}
