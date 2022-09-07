<?php
namespace Apie\Faker\Fakers;

use Apie\Core\Entities\PolymorphicEntityInterface;
use Apie\Core\Other\DiscriminatorMapping;
use Apie\Faker\Interfaces\ApieClassFaker;
use Faker\Generator;
use ReflectionClass;

/**
 * @implements ApieClassFaker<PolymorphicEntityInterface>
 */
class PolymorphicEntityFaker implements ApieClassFaker
{
    public function supports(ReflectionClass $class): bool
    {
        if (!$class->implementsInterface(PolymorphicEntityInterface::class)) {
            return false;
        }
        $method = $class->getMethod('getDiscriminatorMapping');
        return $method->getDeclaringClass()->name === $class->name && !$method->isAbstract();
    }

    public function fakeFor(Generator $generator, ReflectionClass $class): PolymorphicEntityInterface
    {
        /** @var DiscriminatorMapping */
        $mapping = $class->getMethod('getDiscriminatorMapping')->invoke(null);
        $randomConfig = $generator->randomElement($mapping->getConfigs());
        return $generator->fakeClass($randomConfig->getClassname());
    }
}
