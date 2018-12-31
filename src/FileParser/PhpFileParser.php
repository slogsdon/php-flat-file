<?php declare(strict_types=1);

namespace FlatFile\FileParser;

class PhpFileParser extends FileParserAbstract
{
    public function parse(\SplFileInfo $file, \League\Plates\Engine $plates = null): \stdClass
    {
        // ob_start();
        // $required = include $file->getPathName();
        // $output = ob_get_clean();
        $required = 1;

        return (object)[
            'content' => $plates !== null
                ? $plates->render(str_replace(
                    [trim($plates->getDirectory(), '/') . '/', '.' . $file->getExtension()],
                    ['', ''],
                    $file->getPathName()
                ))
                : $output,
            'meta' => [],
        ];
    }

    public function supportedFileExtensions(): array
    {
        return ['php'];
    }
}
