# faker

 [![Latest Stable Version](http://poser.pugx.org/apie/faker/v)](https://packagist.org/packages/apie/faker) [![Total Downloads](http://poser.pugx.org/apie/faker/downloads)](https://packagist.org/packages/apie/faker) [![Latest Unstable Version](http://poser.pugx.org/apie/faker/v/unstable)](https://packagist.org/packages/apie/faker) [![License](http://poser.pugx.org/apie/faker/license)](https://packagist.org/packages/apie/faker) [![PHP Version Require](http://poser.pugx.org/apie/faker/require/php)](https://packagist.org/packages/apie/faker) 

[![PHP Composer](https://github.com/apie-lib/faker/actions/workflows/php.yml/badge.svg?event=push)](https://github.com/apie-lib/faker/actions/workflows/php.yml)

This package is part of the [Apie](https://github.com/apie-lib) library.
The code is maintained in a monorepo, so PR's need to be sent to the [monorepo](https://github.com/apie-lib/apie-lib-monorepo/pulls)

## Documentation
This package adds a method to the library [Faker](https://github.com/FakerPHP/Faker) to fake domain object and value object contents.

Because of the recursive nature this is the easiest setup:
```php
<?php
use Apie\Faker\ApieObjectFaker;
use Faker\Factory;

$faker = Factory::create();
$faker->addProvider(ApieObjectFaker::createWithDefaultFakers($faker));

// returns a random Gender enum value.
$faker->fakeClass(Gender::class);

class User implements EntityInterface
{
    // ...
    public function __construct(private Gender $gender, private FirstName $firstName, private LastName $lastName)
    {
    }
}
// creates a User with random constructor arguments.
$faker->fakeClass(User::class);
```

### String value objects with regular expressions.
String value objects that have the trait IsStringWithRegexValueObject and implement HasRegexValueObjectInterface will
be faked easily by using the regular expression to make a valid value object.

### Adding custom support
You can create a class implementing ApieClassFaker to make your own fake methods. You can reuse the methods available
in Faker itself.

```php
<?php
use Apie\Faker\Interfaces\ApieClassFaker;
use Faker\Generator;
use ReflectionClass;

class SpecificClassFaker implements ApieClassFaker
{
    public function supports(ReflectionClass $class): bool
    {
        return $class->name === SpecificValueObject::class;
    }
    public function fakeFor(Generator $generator, ReflectionClass $class): SpecificValueObject
    {
        return new SpecificValueObject($generator->randomElement([1, 2, 3]));
    }
}
```

### Adding a createRandom method in the object itself.
Instead of making many ApieClassFaker methods you can make a method on the value object that will result in a random
value object.

```php
<?php
use Apie\Core\Attributes\FakeMethod;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;

#[FakeMethod("createRandom")]
class SpecificClass implements ValueObjectInterface
{
    public static function createRandom(): self
    {
        return new self(rand(1, 3));
    }
}
```
You can also provide arguments to this method or the Faker generator itself:
```php
<?php
use Apie\Core\Attributes\FakeMethod;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use Faker\Generator;

#[FakeMethod("createRandom")]
class SpecificClass implements ValueObjectInterface
{
    public static function createRandom(Generator $generator, int $maximum): self
    {
        return new self($generator->numberBetween(1, $maximum));
    }
}
```
In this case calling $faker->fakeClass(SpecificClass::class); will run SpecificClass::createRandom() with the faker and a random integer.
