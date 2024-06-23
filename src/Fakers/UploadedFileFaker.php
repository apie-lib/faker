<?php
namespace Apie\Faker\Fakers;

use Apie\Faker\Interfaces\ApieClassFaker;
use Faker\Generator;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\StreamInterface;
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
        $uploadedFile = new class($contents, $originalName, $mimeType) implements UploadedFileInterface {
            public function __construct(
                private string $contents,
                private string $originalName,
                private string $mimeType
            ) {
            }
            public function getStream(): StreamInterface
            {
                $factory = new Psr17Factory();
                return $factory->createStream($this->contents);
            }
            public function moveTo(string $targetPath): void
            {
                throw new \RuntimeException('Not implemented');
            }
    
            public function getSize(): int
            {
                return strlen($this->contents);
            }
    
            public function getError(): int
            {
                return UPLOAD_ERR_OK;
            }
    
            public function getClientFilename(): string
            {
                return $this->originalName;
            }
            
            public function getClientMediaType(): string
            {
                return $this->mimeType;
            }

        };

        return $uploadedFile;
    }
}
