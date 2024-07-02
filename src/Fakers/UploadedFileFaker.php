<?php
namespace Apie\Faker\Fakers;

use Apie\Core\Other\UploadedFileFactory;
use Apie\Faker\Interfaces\ApieClassFaker;
use Faker\Generator;
use Psr\Http\Message\UploadedFileInterface;
use ReflectionClass;

/** @implements ApieClassFaker<UploadedFileInterface> */
class UploadedFileFaker implements ApieClassFaker
{
    public function supports(ReflectionClass $class): bool
    {
        return $class->name === UploadedFileInterface::class;
    }

    public function fakeFor(Generator $generator, ReflectionClass $class): UploadedFileInterface
    {
        $originalName = $generator->word() . '.txt';
        $mimeType = $generator->mimeType();
        $contents = $generator->text();
        return UploadedFileFactory::createUploadedFileFromString($contents, $originalName, $mimeType);
    }
}
