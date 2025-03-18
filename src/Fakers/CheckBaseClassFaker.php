<?php

namespace Apie\Faker\Fakers;

use Apie\Core\Attributes\FakeMethod;
use Apie\Faker\Interfaces\ApieClassFaker;
use Faker\Generator;
use ReflectionAttribute;
use ReflectionClass;

/**
 * @template T of object
 * @implements ApieClassFaker<T>
 */
class CheckBaseClassFaker implements ApieClassFaker
{
    private string $method;

    /**
     * @var array<class-string<T>, array<int, ReflectionAttribute<FakeMethod>>> $cached
     */
    private array $cached = [];

    /**
     * @param ReflectionClass<T> $baseClass
     */
    public function __construct(private ReflectionClass $baseClass)
    {
        $this->method = $baseClass->isInterface() ? 'implementsInterface' : 'isSubclassOf';
    }

    public function supports(ReflectionClass $class): bool
    {
        if (isset($this->cached[$class->name])) {
            return (bool) $this->cached[$class->name];
        }
        if ($class->{$this->method}($this->baseClass)) {
            $parent = $class;
            do {
                $parent = $parent->getParentClass();
                if ($parent && $parent->getAttributes(FakeMethod::class)) {
                    $this->cached[$class->name] = $parent->getAttributes(FakeMethod::class);
                    return true;
                }
            } while ($parent);
        }
        return false;
    }

    public function fakeFor(Generator $generator, ReflectionClass $class): object
    {
        return UseFakeMethodFaker::runFake($generator, $class, $this->cached[$class->name] ?? []);
    }
}
