<?php
namespace Apie\Faker\Fakers;

use Apie\Core\ValueObjects\CompositeValueObject;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use Apie\Faker\Interfaces\ApieClassFaker;
use Apie\TypeConverter\ReflectionTypeFactory;
use Faker\Generator;
use ReflectionClass;

/** @implements ApieClassFaker<ValueObjectInterface> */
class CompositeObjectFaker implements ApieClassFaker
{
    public function supports(ReflectionClass $class): bool
    {
        $interfaces = $class->getInterfaceNames();
        return in_array(ValueObjectInterface::class, $interfaces) &&
            in_array(CompositeValueObject::class, $class->getTraitNames());
    }

    public function fakeFor(Generator $generator, ReflectionClass $class): ValueObjectInterface
    {
        $data = [];
        $className = $class->name;
        // @phpstan-ignore-next-line staticMethod.notFound
        foreach ($className::getFields() as $name => $field) {
            if ($field->isOptional() && $generator->boolean()) {
                continue;
            }
            $data[$name] = $generator->fakeFromType(
                ReflectionTypeFactory::createReflectionType($field->getTypehint()),
            );
        }
        return $className::fromNative($data);
    }
}
