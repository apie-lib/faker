<?php
namespace Apie\Faker\FileFakers;

use Apie\Faker\Interfaces\ApieFileFaker;
use Faker\Generator;
use Symfony\Component\Finder\Finder;
use ZipStream\ZipStream;

final class WordDocumentFaker implements ApieFileFaker
{
    public function createOriginalFilename(Generator $faker): string
    {
        return $faker->word() . '.docx';
    }
    public function createMimeType(): string
    {
        return 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    }
    public static function isSupported(): bool
    {
        return true;
    }

    /** @return resource */
    public function createResource(Generator $faker, string $originalFilename, string $mimeType): mixed
    {
        $resource = tmpfile();
        $zipstream = new ZipStream(
            outputStream: $resource,
            sendHttpHeaders: false,
            outputName: $originalFilename,
            contentType: $mimeType,
            defaultEnableZeroHeader: true,
        );
        foreach (Finder::create()->files()->ignoreDotFiles(false)->in(__DIR__ . '/../../fixtures/docx') as $file) {
            $zipstream->addFileFromPath($file->getRelativePathname(), $file->getRealPath());
        }
        $documentXML = file_get_contents(__DIR__ . '/../../fixtures/document.xml');
        $documentXML = str_replace('LORUM IPSUM', htmlentities($faker->text(1024)), $documentXML);
        $zipstream->addFile('word/document.xml', $documentXML);
        //$imageFile = $faker->fakeClass(BackgroundFileFaker::class);
        //$zipstream->addFileFromStream('word/media/image1.jpg', $imageFile->createResource($faker, 'image1.jpg', 'image/jpeg'));
        // TODO replace image randomly
        $zipstream->finish();
        return $resource;
    }
}
