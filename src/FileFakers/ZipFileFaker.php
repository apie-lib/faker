<?php
namespace Apie\Faker\FileFakers;

use Apie\Faker\Interfaces\ApieFileFaker;
use Apie\Faker\SeededFile;
use Faker\Generator;
use ZipStream\ZipStream;

final class ZipFileFaker implements ApieFileFaker
{
    private static bool $alreadyCreating = false;
    public function createOriginalFilename(Generator $faker): string
    {
        return $faker->word() . '.zip';
    }
    public function createMimeType(): string
    {
        return 'application/zip';
    }
    public static function isSupported(): bool
    {
        return !self::$alreadyCreating;
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
        $counter = $faker->numberBetween(1, 12);
        self::$alreadyCreating = true;
        try {
            for ($i = 0; $i < $counter; $i++) {
                /** @var SeededFile $file */
                $file = $faker->fakeClass(SeededFile::class);
                $zipstream->addFileFromPsr7Stream(
                    fileName: $file->getClientFilename(),
                    stream: $file->getStream(),
                );
            }
            $zipstream->finish();
        } finally {
            self::$alreadyCreating = false;
        }
        return $resource;
    }
}
