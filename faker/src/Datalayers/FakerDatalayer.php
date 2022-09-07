<?php
namespace Apie\Faker\Datalayers;

use Apie\Core\BoundedContext\BoundedContext;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Datalayers\BoundedContextAwareApieDatalayer;
use Apie\Core\Datalayers\Lists\LazyLoadedList;
use Apie\Core\Datalayers\ValueObjects\LazyLoadedListIdentifier;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\Identifiers\AutoIncrementInteger;
use Apie\Core\Identifiers\IdentifierInterface;
use Apie\Core\IdentifierUtils;
use Faker\Generator;
use ReflectionClass;

class FakerDatalayer implements BoundedContextAwareApieDatalayer
{
    public function __construct(private readonly Generator $faker)
    {
    }

    public function all(ReflectionClass $class, ?BoundedContext $boundedContext = null): LazyLoadedList
    {
        $count = new CountFakeData($class);
        return new LazyLoadedList(
            LazyLoadedListIdentifier::createFrom(
                $boundedContext ? $boundedContext->getId() : new BoundedContextId('unknown'),
                $class
            ),
            new ProvideSingleFakeData($class, $this->faker),
            new ProvideMultipleFakeData($class, $this->faker, $count),
            $count
        );
    }

    public function find(IdentifierInterface $identifier, ?BoundedContext $boundedContext = null): EntityInterface
    {
        $class = IdentifierUtils::identifierToEntityClass($identifier);
        $object = $this->faker->fakeClass($class->name);
        IdentifierUtils::injectIdentifier($object, $identifier);
        return $object;
    }

    public function persistNew(EntityInterface $entity, ?BoundedContext $boundedContext = null): EntityInterface
    {
        $identifier = $entity->getId();
        if ($identifier instanceof AutoIncrementInteger) {
            $className = get_class($identifier);
            IdentifierUtils::injectIdentifier($entity, $className::createRandom($this->faker));
        }
        return $entity;
    }

    public function persistExisting(EntityInterface $entity, ?BoundedContext $boundedContext = null): EntityInterface
    {
        return $entity;
    }
}
