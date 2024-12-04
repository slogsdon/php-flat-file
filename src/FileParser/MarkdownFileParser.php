<?php declare(strict_types=1);

namespace FlatFile\FileParser;

use function FlatFile\Functions\markdown;

class MarkdownFileParser extends FileParserAbstract
{
    public function parse(\SplFileInfo $file, ?\League\Plates\Engine $plates = null): ParsedFile
    {
        $contents = file_get_contents($file->getPathName());
        if (false === $contents) {
            return new ParsedFile;
        }
        $result = markdown($contents);
        if (isset($result->meta['layout']) && $plates !== null) {
            $template = $plates->make($result->meta['layout']);
            $result->content = $template->render($result->meta + ['content' => $result->content]);
        }
        return $result;
    }

    /**
     * @return array<string>
     */
    public function supportedFileExtensions(): array
    {
        return ['md', 'markdown'];
    }
}
