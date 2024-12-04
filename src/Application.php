<?php declare(strict_types=1);

namespace FlatFile;

use function FlatFile\Functions\markdown;

class Application
{
    /**
     * Preferred datetime format. Mirrors format used by PHP's
     * built-in web server.
     *
     * @var string
     */
    const DATETIME_FORMAT = 'D M j H:i:s Y';

    /**
     * Request URI index on `$_SERVER` superglobal
     *
     * @var string
     */
    const SERVER_REQUEST_URI = 'REQUEST_URI';

    /**
     * Current instance options
     *
     * @var array<string, string|bool>
     */
    private $options;

    /**
     * Application's discovered page objects
     *
     * @var array<Page>
     */
    private $pages;

    /**
     * Filesystem helper
     *
     * @var Files
     */
    private $files;

    /**
     * Obtains correct file parser based on file attributes
     *
     * @var FileParserFactory
     */
    private $fileParserFactory;

    /**
     * Template engine
     *
     * @var \League\Plates\Engine
     */
    private $plates;

    /**
     * Create a new application instance
     *
     * ### Options
     *
     * - `pagesPath` - Preferred directory containing site pages
     * - `resourcesPath` - Preferred directory containing site resources
     * - `requestUri` - Fallback request URI when `$_SERVER['REQUEST_URI']`
     *     is not set
     * - `noRun` - Skip default output of page matching `requestUri`
     *
     * @param array<string, string|bool> $options See above for available options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
        $this->files = new Files;
        $this->fileParserFactory = new FileParserFactory;
        $this->pages = $this->findPages();
        $this->prepareTemplates();

        if (isset($options['noRun']) && true === $options['noRun']) {
            return;
        }

        $this->outputResult(
            $this->getRequestedContent()
        );
    }

    public function prepareTemplates(): void
    {
        $pagesPath = $this->getPagesPath();
        $resourcesPath = $this->getResourcesPath();

        if (!is_bool($pagesPath)) {
            $this->plates = new \League\Plates\Engine($pagesPath);
        }

        if ($this->plates !== null && !is_bool($resourcesPath)) {
            $this->plates->addFolder('partials', $resourcesPath . '/partials');
        }
    }

    /**
     * Produces array of page objects from discovered pages,
     * indexed by slug
     *
     * @return array<Page>
     */
    public function findPages(): array
    {
        $foundPages = [];
        $root = $this->getPagesPath();

        if ($root) {
            $root .=  '/';

            foreach ($this->generatePageFiles($root) as $file) {
                $slug = $this->getSlug($root, $file);
                $foundPages[$slug] = new Page(
                    $file,
                    $this->buildContentFunction($file),
                    $slug,
                    count(explode('/', $slug))
                );
            }
        }

        return $foundPages;
    }

    /**
     * @return string|boolean
     */
    protected function getPagesPath()
    {
        $option = $this->getOption('pagesPath');
        if (!is_string($option)) {
            $option = '';
        }
        return realpath($option ?: getcwd() . '/pages');
    }

    /**
     * @return string|boolean
     */
    protected function getResourcesPath()
    {
        $option = $this->getOption('resourcesPath');
        if (!is_string($option)) {
            $option = '';
        }
        return realpath($option ?: getcwd() . '/resources');
    }

    /**
     * Helper function to set `requestUri` option and get
     * result for supplied `$slug`
     *
     * @param string $slug Desired page slug to retrieve
     *
     * @return array<int|string|FileParser\ParsedFile|null>
     */
    public function getContentFor(string $slug): array
    {
        $this->setOption('requestUri', '/' . trim($slug, '/'));
        return $this->getRequestedContent();
    }

    /**
     * @return array{0: int, 1: FileParser\ParsedFile|string}
     */
    public function getRequestedContent(): array
    {
        $rawUri =
            isset($_SERVER[static::SERVER_REQUEST_URI])
            ? $_SERVER[static::SERVER_REQUEST_URI]
            : $this->getOption('requestUri');
        if (!is_string($rawUri)) {
            $rawUri = '/';
        }
        $requestUri = trim($rawUri, '/');

        if (isset($this->pages[$requestUri])) {
            $content = $this->pages[$requestUri]->content;
            /** @var FileParser\ParsedFile $result */
            $result = ($content)();
            return [200, $result];
        }

        if ($requestUri === '' && isset($this->pages['index'])) {
            $content = $this->pages['index']->content;
            /** @var FileParser\ParsedFile $result */
            $result = ($content)();
            return [200, $result];
        }

        return [404, 'not found'];
    }

    /**
     * @param array<int|string|FileParser\ParsedFile|null> $result
     */
    public function outputResult(array $result): void
    {
        list($status, $content) = $result;
        if (is_int($status)) {
            http_response_code($status);
        }
        $this->logAccess();
        if (isset($content) && $content instanceof FileParser\ParsedFile) {
            print $content->content;
        } elseif (isset($content) && is_string($content)) {
            print $content;
        }
    }

    public static function resolveRouter(): string
    {
        $publicIndex = getcwd() . '/public/index.php';

        if (is_file($publicIndex)) {
            return $publicIndex;
        }

        return __DIR__ . '/router.php';
    }

    public function setOption(string $key, string|bool $value): self
    {
        $this->options[$key] = $value;
        return $this;
    }

    protected function buildContentFunction(\SplFileInfo $file): callable
    {
        $parser = $this->fileParserFactory->createFrom($file);
        return function () use ($parser, $file) {
            return $parser->parse($file, $this->plates);
        };
    }

    /**
     * @param string $root
     *
     * @return iterable<\SplFileInfo>
     */
    protected function generatePageFiles(string $root): iterable
    {
        yield from $this->files->findAll($root);
    }

    public function getOption(string $key): string|bool|null
    {
        return isset($this->options[$key]) ? $this->options[$key] : null;
    }

    protected function getSlug(string $root, \SplFileInfo $file): string
    {
        return trim(str_replace(
            $root,
            '',
            ($file->getPath() . '/' . str_replace('.' . $file->getExtension(), '', $file->getFileName()))
        ), '/');
    }

    protected function logAccess(): void
    {
        $remoteAddr = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        $remotePort = isset($_SERVER['REMOTE_PORT']) ? $_SERVER['REMOTE_PORT'] : '';
        $status = http_response_code();
        $requestUri = isset($_SERVER[static::SERVER_REQUEST_URI])
            ? $_SERVER[static::SERVER_REQUEST_URI]
            : '';

        error_log(sprintf(
            '%s:%s [%s]: %s',
            is_string($remoteAddr) ? $remoteAddr : '',
            is_string($remotePort) ? $remotePort : '',
            $status,
            is_string($requestUri) ? $requestUri : ''
        ));
    }
}
