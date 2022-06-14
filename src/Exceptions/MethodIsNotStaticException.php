<?php
namespace Apie\Faker\Exceptions;

use Apie\Core\Exceptions\ApieException;
use ReflectionMethod;

class MethodIsNotStaticException extends ApieException
{
    public function __construct(ReflectionMethod $method)
    {
        parent::__construct(sprintf('Class method "%s" is not static!', $method->getName()));
    }
}
