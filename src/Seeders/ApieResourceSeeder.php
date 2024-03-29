<?php
namespace Apie\Faker\Seeders;

use Apie\Core\Actions\BoundedContextEntityTuple;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\Identifiers\IdentifierInterface;
use Apie\Faker\Interfaces\ApieClassFaker;
use Faker\Generator;
use LogicException;
use ReflectionClass;

/**
 * @implements ApieClassFaker<IdentifierInterface>
 */
final class ApieResourceSeeder implements ApieClassFaker
{
    /**
     * @var array<int, EntityInterface|null> $createdResources
     */
    private array $createdResources = [];

    /**
     * @var array<string, bool> $idsCreated;
     */
    private array $idsCreated = [];

    private bool $building = false;

    public function __construct(
        private readonly BoundedContextEntityTuple $contextAndClass,
        private readonly int $amount
    ) {
        $this->createdResources = array_fill(0, $amount, null);
    }

    /**
     * @param ReflectionClass<object> $class
     */
    public function supports(ReflectionClass $class): bool
    {
        if ($this->building || !$class->implementsInterface(IdentifierInterface::class)) {
            return false;
        }
        $intendedClass = $class->getMethod('getReferenceFor')->invoke(null);
        return $intendedClass->name === $this->contextAndClass->resourceClass->name;
    }

    /**
     * @return ReflectionClass<EntityInterface>
     */
    public function getResourceClass(): ReflectionClass
    {
        return $this->contextAndClass->resourceClass;
    }

    public function getBoundedContextId(): BoundedContextId
    {
        return $this->contextAndClass->boundedContext->getId();
    }

    public function getResource(Generator $generator, int $index): ?EntityInterface
    {
        if ($index < 0 || $index >= $this->amount) {
            return null;
        }
        if (!isset($this->createdResources[$index])) {
            $retries = 0;
            $this->building = true;
            try {
                do {
                    $fakedResource = $generator->fakeClass($this->contextAndClass->resourceClass->name);
                    $id = $fakedResource->getId()->toNative();
                    $retries++;
                } while ($id !== null && isset($this->idsCreated[$id]) && $retries < 1000);
                if ($id !== null && isset($this->idsCreated[$id])) {
                    throw new LogicException(
                        sprintf(
                            'I tried to create a unique resource, but it failed for 1000 times on class "%s"!',
                            $this->contextAndClass->resourceClass->name
                        )
                    );
                }
                $this->idsCreated[$id] = true;
                $this->createdResources[$index] = $fakedResource;
            } finally {
                $this->building = false;
            }
        }

        return $this->createdResources[$index];
    }

    /**
     * @template T of IdentifierInterface<EntityInterface>
     * @param ReflectionClass<T> $class
     * @return T
     */
    public function fakeFor(Generator $generator, ReflectionClass $class): IdentifierInterface
    {
        return $this->getResource($generator, $generator->numberBetween(0, $this->amount - 1))->getId();
    }
}
