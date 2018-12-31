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
     * @var array
     */
    private $options;

    /**
     * Application's discovered page objects
     *
     * @var array
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
     * @param array $options See above for available options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
        $this->files = new Files;
        $this->fileParserFactory = new FileParserFactory;
        $this->pages = $this->findPages();
        $this->plates = new \League\Plates\Engine($this->getPagesPath());
        $this->plates->addFolder('partials', $this->getResourcesPath() . '/partials');

        if (isset($options['noRun']) && true === $options['noRun']) {
            return;
        }

        $this->outputResult(
            $this->getRequestedContent()
        );
    }

    /**
     * Produces array of page objects from discovered pages,
     * indexed by slug
     *
     * @return array
     */
    public function findPages(): array
    {
        $foundPages = [];
        $root = $this->getPagesPath();

        if ($root) {
            $root .=  '/';

            foreach ($this->generatePageFiles($root) as $file) {
                $slug = $this->getSlug($root, $file);
                $foundPages[$slug] = (object)[
                    'info'=> $file,
                    'content' => $this->buildContentFunction($file),
                    'slug' => $slug,
                    'depth' => count(explode('/', $slug)),
                ];
            }
        }

        return $foundPages;
    }

    protected function getPagesPath(): string
    {
        return realpath($this->getOption('pagesPath') ?: getcwd() . '/pages');
    }

    protected function getResourcesPath(): string
    {
        return realpath($this->getOption('resourcesPath') ?: getcwd() . '/resources');
    }

    /**
     * Helper function to set `requestUri` option and get
     * result for supplied `$slug`
     *
     * @param string $slug Desired page slug to retrieve
     * @return array
     */
    public function getContentFor(string $slug): array
    {
        $this->setOption('requestUri', '/' . trim($slug, '/'));
        return $this->getRequestedContent();
    }

    public function getRequestedContent(): array
    {
        $rawUri =
            isset($_SERVER[static::SERVER_REQUEST_URI])
            ? $_SERVER[static::SERVER_REQUEST_URI]
            : $this->getOption('requestUri');
        $requestUri = trim($rawUri, '/');

        if (isset($this->pages[$requestUri])) {
            return [200, $this->pages[$requestUri]->content->call($this)];
        }

        if ($requestUri === '' && isset($this->pages['index'])) {
            return [200, $this->pages['index']->content->call($this)];
        }

        return [404, 'not found'];
    }

    public function outputResult(array $result): void
    {
        list($status, $content) = $result;
        http_response_code($status);
        $this->logAccess();
        print $content->content;
    }

    public static function resolveRouter(): string
    {
        $publicIndex = getcwd() . '/public/index.php';

        if (is_file($publicIndex)) {
            return $publicIndex;
        }

        return __DIR__ . '/router.php';
    }

    public function setOption(string $key, $value): self
    {
        $this->options[$key] = $value;
        return $this;
    }

    protected function buildContentFunction(\SplFileInfo $file)
    {
        $parser = $this->fileParserFactory->createFrom($file);
        return function () use ($parser, $file) {
            return $parser->parse($file, $this->plates);
        };
    }

    protected function generatePageFiles(string $root): iterable
    {
        yield from $this->files->findAll($root);
    }

    public function getOption(string $key)
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

    protected function logAccess()
    {
        $remoteAddr = $_SERVER['REMOTE_ADDR'];
        $remotePort = $_SERVER['REMOTE_PORT'];
        $status = http_response_code();
        $requestUri = $_SERVER[static::SERVER_REQUEST_URI];

        error_log(sprintf(
            '%s:%s [%s]: %s',
            $remoteAddr,
            $remotePort,
            $status,
            $requestUri
        ));
    }
}
