<?php
namespace Apie\Tests\Faker\Concerns;

use Apie\Faker\ApieObjectFaker;
use Faker\Factory;
use Faker\Generator;

trait ItCreatesAFaker
{
    private function givenAFakerWithApieObjectFaker(): Generator
    {
        $faker = Factory::create();
        $faker->addProvider(ApieObjectFaker::createWithDefaultFakers($faker));
        return $faker;
    }
}
