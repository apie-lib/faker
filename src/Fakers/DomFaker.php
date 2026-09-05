<?php
namespace Apie\Faker\Fakers;

use Apie\Faker\Interfaces\ApieClassFaker;
use DOMAttr;
use DOMDocument;
use DOMElement;
use Faker\Generator;
use ReflectionClass;

/** @implements ApieClassFaker<DOMAttr|DOMElement> */
class DomFaker implements ApieClassFaker
{
    public function supports(ReflectionClass $class): bool
    {
        return in_array($class->name, [DOMAttr::class, DOMElement::class], true);
    }

    public function fakeFor(Generator $generator, ReflectionClass $class): object
    {
        if ($class->name === DOMAttr::class) {
            return new DOMAttr('value', 'content');
        }

        $document = new DOMDocument();
        return $document->createElement('root', 'content');
    }
}
