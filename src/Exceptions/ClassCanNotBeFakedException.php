<?php
namespace Apie\Faker\Exceptions;

use Apie\Core\Exceptions\ApieException;
use ReflectionClass;

class ClassCanNotBeFakedException extends ApieException
{
    public function __construct(ReflectionClass $class)
    {
        parent::__construct(sprintf('Class "%s" can not faked!', $class->name));
    }
}
