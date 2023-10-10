<?php
namespace Apie\Faker\Datalayers;

use Apie\Core\BoundedContext\BoundedContext;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Datalayers\ApieDatalayerWithFilters;
use Apie\Core\Datalayers\BoundedContextAwareApieDatalayer;
use Apie\Core\Datalayers\Concerns\FiltersOnAllFields;
use Apie\Core\Datalayers\Lists\EntityListInterface;
use Apie\Core\Datalayers\Search\LazyLoadedListFilterer;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\Identifiers\AutoIncrementInteger;
use Apie\Core\Identifiers\IdentifierInterface;
use Apie\Core\IdentifierUtils;
use Faker\Generator;
use ReflectionClass;

class FakerDatalayer implements ApieDatalayerWithFilters, BoundedContextAwareApieDatalayer
{
    use FiltersOnAllFields;

    public function __construct(private readonly Generator $faker, private readonly LazyLoadedListFilterer $filterer)
    {
    }

    public function all(ReflectionClass $class, ?BoundedContext $boundedContext = null): EntityListInterface
    {
        return new FakeEntityList(
            $class,
            $boundedContext ? $boundedContext->getId() : new BoundedContextId('unknown'),
            $this->filterer,
            $this->faker
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

    public function removeExisting(EntityInterface $entity, ?BoundedContext $boundedContext = null): void
    {
    }
}
