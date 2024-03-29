<?php
namespace Apie\Faker;

use Apie\Core\Exceptions\InvalidTypeException;
use Apie\Core\Identifiers\IdentifierInterface;
use Apie\Faker\Exceptions\ClassCanNotBeFakedException;
use Apie\Faker\Fakers\CheckBaseClassFaker;
use Apie\Faker\Fakers\DateValueObjectFaker;
use Apie\Faker\Fakers\EnumFaker;
use Apie\Faker\Fakers\ItemHashmapFaker;
use Apie\Faker\Fakers\ItemListFaker;
use Apie\Faker\Fakers\PasswordValueObjectFaker;
use Apie\Faker\Fakers\PhpDateTimeObjectFaker;
use Apie\Faker\Fakers\PolymorphicEntityFaker;
use Apie\Faker\Fakers\StringValueObjectWithRegexFaker;
use Apie\Faker\Fakers\UseConstructorFaker;
use Apie\Faker\Fakers\UseFakeMethodFaker;
use Apie\Faker\Interfaces\ApieClassFaker;
use Apie\TypeConverter\ReflectionTypeFactory;
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
     * @var array<ApieClassFaker<object>> $fakers
     */
    private array $fakers;

    /**
     * @param ApieClassFaker<object> $fakers
     */
    public function __construct(Generator $generator, ApieClassFaker... $fakers)
    {
        $this->fakers = $fakers;
        parent::__construct($generator);
    }

    /**
     * @param ApieClassFaker<object> $additional
     */
    public static function createWithDefaultFakers(Generator $generator, ApieClassFaker... $additional): self
    {
        return new self(
            $generator,
            ...[
                ...$additional,
                new UseFakeMethodFaker(),
                new CheckBaseClassFaker(new ReflectionClass(IdentifierInterface::class)),
                new PolymorphicEntityFaker(),
                new ItemListFaker(),
                new ItemHashmapFaker(),
                new PasswordValueObjectFaker(),
                new DateValueObjectFaker(),
                new StringValueObjectWithRegexFaker(),
                new EnumFaker(),
                new PhpDateTimeObjectFaker(),
                new UseConstructorFaker(),
            ]
        );
    }

    /**
     * @template T of object
     * @param class-string<T> $className
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

    /**
     * @return array<int, mixed>
     */
    public function fakeArgumentsOfMethod(ReflectionMethod $method): array
    {
        $arguments = [];
        foreach ($method->getParameters() as $parameter) {
            $type = $parameter->getType();
            if ($parameter->isVariadic()) {
                $rand = $this->generator->numberBetween(0, 4);
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

    public function fakeMixed(): mixed
    {
        return $this->fakeFromType(ReflectionTypeFactory::createReflectionType('array|null|bool|float|string|int'));
    }

    public function fakeFromType(?ReflectionType $typehint): mixed
    {
        if ($typehint === null) {
            return $this->fakeMixed();
        }
        if ($typehint instanceof ReflectionIntersectionType) {
            throw new InvalidTypeException($typehint, 'ReflectionUnionType|ReflectionNamedType');
        }
        $types = $typehint instanceof ReflectionUnionType ? $typehint->getTypes() : [$typehint];
        $type = $this->generator->randomElement($types);
        if ($type->getName() === Generator::class) {
            return $this->generator;
        }
        if ($type->allowsNull()) {
            if ($this->generator->boolean()) {
                return null;
            }
        }
        return match ($type->getName()) {
            'array' => [
                $this->generator->word() => $this->generator->fakeMixed(),
                $this->generator->word() => $this->generator->fakeMixed(),
                $this->generator->word() => $this->generator->fakeMixed(),
            ],
            'null' => null,
            'false' => false,
            'true' => true,
            'bool' => $this->generator->randomElement([true, false]),
            'float' => $this->generator->randomFloat(),
            'string' => $this->generator->word(),
            'int' => $this->generator->numberBetween(-2147483648, 2147483647), // compatible with integers in Mysql
            'mixed' => $this->fakeMixed(),
            default => $this->fakeClass($type->getName()),
        };
    }
}
