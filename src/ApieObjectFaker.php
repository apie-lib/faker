<?php
namespace Apie\Faker;

use Apie\Core\Exceptions\InvalidTypeException;
use Apie\Faker\Exceptions\ClassCanNotBeFakedException;
use Apie\Faker\Fakers\DateValueObjectFaker;
use Apie\Faker\Fakers\EnumFaker;
use Apie\Faker\Fakers\PasswordValueObjectFaker;
use Apie\Faker\Fakers\PolymorphcEntityFaker;
use Apie\Faker\Fakers\PolymorphicEntityFaker;
use Apie\Faker\Fakers\StringValueObjectWithRegexFaker;
use Apie\Faker\Fakers\TextValueObjectFaker;
use Apie\Faker\Fakers\UseConstructorFaker;
use Apie\Faker\Fakers\UseDefaultGeneratorFaker;
use Apie\Faker\Fakers\UseFakeMethodFaker;
use Apie\Faker\Interfaces\ApieClassFaker;
use Faker\Generator;
use Faker\Provider\Base;
use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionMethod;
use ReflectionType;
use ReflectionUnionType;

/**
 * This is a stub class
 */
final class ApieObjectFaker extends Base
{
    /**
     * @ApieClassFaker[]
     */
    private array $fakers;

    public function __construct(Generator $generator, ApieClassFaker... $fakers)
    {
        $this->fakers = $fakers;
        parent::__construct($generator);
    }

    public static function createWithDefaultFakers(Generator $generator, ApieClassFaker... $additional): self
    {
        return new self(
            $generator,
            new UseFakeMethodFaker(),
            new PolymorphicEntityFaker(),
            new PasswordValueObjectFaker(),
            new TextValueObjectFaker(),
            new UseDefaultGeneratorFaker(),
            new DateValueObjectFaker(),
            new StringValueObjectWithRegexFaker(),
            new EnumFaker(),
            new UseConstructorFaker(),
            ...$additional
        );
    }

    /**
     * @template T of object
     * @param class-string<T>
     * @return T
     */
    public function fakeClass(string $className): object
    {
        $refl = new ReflectionClass($className);
        foreach ($this->fakers as $faker) {
            if ($faker->supports($refl)) {
                return $faker->fakeFor($this->generator, $refl);
            }
        }

        throw new ClassCanNotBeFakedException($refl);
    }

    public function fakeArgumentsOfMethod(ReflectionMethod $method): array
    {
        $arguments = [];
        foreach ($method->getParameters() as $parameter) {
            $type = $parameter->getType();
            if ($parameter->isVariadic()) {
                $rand = $this->generator->rand(0, 4);
                for ($i = 0; $i < $rand; $i++) {
                    $arguments[] = $this->generator->fakeFromType($type);
                }
            } elseif ($parameter->allowsNull() && 1 === $this->generator->numberBetween(0, 4)) {
                $arguments[] = null;
            } else {
                $arguments[] = $this->generator->fakeFromType($type);
            }
        }

        return $arguments;
    }
    
    public function fakeFromType(?ReflectionType $typehint): mixed
    {
        if ($typehint === null) {
            return null;
        }
        if ($typehint instanceof ReflectionIntersectionType) {
            throw new InvalidTypeException($typehint, 'ReflectionUnionType|ReflectionNamedType');
        }
        $types = $typehint instanceof ReflectionUnionType ? $typehint->getTypes() : [$typehint];
        $type = $this->generator->randomElement($types);
        if ($type->getName() === Generator::class) {
            return $this->generator;
        }
        return $this->fakeClass($type->getName());
    }
}
