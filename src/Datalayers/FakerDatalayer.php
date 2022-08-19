<?php
namespace Apie\Faker\Datalayers;

use Apie\Core\BoundedContext\BoundedContext;
use Apie\Core\Datalayers\BoundedContextAwareApieDatalayer;
use Apie\Core\Datalayers\Lists\LazyLoadedList;
use Apie\Core\Datalayers\ValueObjects\LazyLoadedListIdentifier;
use Apie\Core\Entities\EntityInterface;
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
        return new LazyLoadedList(
            LazyLoadedListIdentifier::createFrom($boundedContext->getId(), $class),
            new ProvideSingleFakeData($class, $this->faker),
            new ProvideMultipleFakeData($class, $this->faker),
            new CountFakeData($class)
        );
    }

    public function find(IdentifierInterface $identifier, ?BoundedContext $boundedContext = null): EntityInterface
    {
        $class = IdentifierUtils::identifierToEntityClass($identifier);
        return $this->faker->fakeClass($class->name);
    }

    public function persistNew(EntityInterface $entity, ?BoundedContext $boundedContext = null): EntityInterface
    {
        // TODO check AutoIncrementInteger
        return $entity;
    }

    public function persistExisting(EntityInterface $entity, ?BoundedContext $boundedContext = null): EntityInterface
    {
        return $entity;
    }
}
