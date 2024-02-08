<?php

namespace App\uploader;

use App\container\Container;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Io\UploadedFile;

class File
{
    public static function upload(ServerRequestInterface $request): string
    {
        /** @var UploadedFile $file */
        $file = $request->getUploadedFiles()['image'];
        $extension = self::getFileExtension($file->getClientFilename());

        return self::makeFilePath(
            $file->getStream()->getContents(),
            $extension
        );
    }

    private static function getFileExtension(string $fileName): string
    {
        preg_match('/^.*\.(.+)$/', $fileName, $fileNameParsed);
        return $fileNameParsed[1];
    }

    private static function makeFilePath(mixed $content, string $extension): string
    {
        $fileName = md5($content);
        $basePath = Container::getInstance()->get('BASE_PATH');
        file_put_contents("{$basePath}/uploads/$fileName.$extension", $content);
        return "{$basePath}/uploads/$fileName.$extension";
    }
}