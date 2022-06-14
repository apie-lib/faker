<?php
namespace Apie\Faker\Fakers;

use Apie\Core\Attributes\FakeMethod;
use Apie\Core\Exceptions\InvalidTypeException;
use Apie\Faker\Exceptions\MethodIsNotStaticException;
use Apie\Faker\Interfaces\ApieClassFaker;
use Faker\Generator;
use ReflectionAttribute;
use ReflectionClass;

/** @implements ApieClassFaker<object> */
class UseFakeMethodFaker implements ApieClassFaker
{
    public function supports(ReflectionClass $class): bool
    {
        return !empty($class->getAttributes(FakeMethod::class));
    }
    public function fakeFor(Generator $generator, ReflectionClass $class): object
    {
        /** @var ReflectionAttribute<FakeMethod> $fakeMethod */
        $fakeMethod = $generator->randomElement($class->getAttributes(FakeMethod::class));
        $method = $class->getMethod($fakeMethod->newInstance()->methodName);
        if (!$method->isStatic()) {
            throw new MethodIsNotStaticException($method);
        }
        $arguments = [];
        foreach ($method->getParameters() as $parameter) {
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
        $object = $method->invokeArgs(null, $arguments);
        if (!$class->isInstance($object)) {
            throw new InvalidTypeException($object, $class->name);
        }
        return $object;
    }
}
