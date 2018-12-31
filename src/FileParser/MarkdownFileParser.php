<?php declare(strict_types=1);

namespace FlatFile\FileParser;

use function FlatFile\Functions\markdown;

class MarkdownFileParser extends FileParserAbstract
{
    public function parse(\SplFileInfo $file, \League\Plates\Engine $plates = null): \stdClass
    {
        $contents = file_get_contents($file->getPathName());
        if (false === $contents) {
            return (object)[
                'content' => '',
                'meta' => null,
            ];
        }
        $result = markdown($contents);
        if (isset($result->meta['layout'])) {
            $template = $plates->make($result->meta['layout']);
            $result->content = $template->render($result->meta + ['content' => $result->content]);
        }
        return $result;
    }

    public function supportedFileExtensions(): array
    {
        return ['md', 'markdown'];
    }
}
