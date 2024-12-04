<?php declare(strict_types=1);

namespace FlatFile;

class Page
{
    /**
     * @var \SplFileInfo
     */
    public $file;

    /**
     * @var callable
     * @return FileParser\ParsedFile
     */
    public $content;

    /**
     * @var string
     */
    public $slug;

    /**
     * @var int
     */
    public $depth;

    /**
     * Create a new Page instance
     *
     * @param \SplFileInfo $file The file object representing the page
     * @param callable $content Function that returns the page content
     * @param string $slug The URL slug for the page
     * @param int $depth The depth of the page in the site hierarchy
     */
    public function __construct(
        \SplFileInfo $file,
        callable $content,
        string $slug,
        int $depth
    ) {
        $this->file = $file;
        $this->content = $content;
        $this->slug = $slug;
        $this->depth = $depth;
    }
}
