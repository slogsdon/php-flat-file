<?php declare(strict_types=1);

namespace FlatFile\Functions;

use League\CommonMark\GithubFlavoredMarkdownConverter;
use Spatie\YamlFrontMatter\YamlFrontMatter;
use FlatFile\FileParser\ParsedFile;

function markdown(string $markdown): ParsedFile
{
    /** @var GithubFlavoredMarkdownConverter|null $converter */
    static $converter;
    if (!$converter) {
        $converter = new GithubFlavoredMarkdownConverter([]);
    }

    $content = YamlFrontMatter::parse($markdown);
    $result = new ParsedFile;

    // Convert the markdown content
    $converted = $converter->convert($content->body());
    $result->content = $converted->getContent();

    /** @var array<string, string> $meta */
    $meta = $content->matter();
    if (empty($meta)) {
        $meta = [];
    }

    $result->meta = $meta;

    return $result;
}
