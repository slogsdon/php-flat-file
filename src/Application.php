<?php declare(strict_types=1);

namespace FlatFile;

use function FlatFile\Functions\markdown;

class Application
{
    /** @var string */
    const DATETIME_FORMAT = 'D M j H:i:s Y';
    /** @var string */
    const SERVER_REQUEST_URI = 'REQUEST_URI';

    /** @var array */
    private $options;
    /** @var array */
    private $pages;
    /** @var Files */
    private $files;

    public function __construct(array $options = [])
    {
        $this->options = $options;
        $this->files = new Files;
        $this->pages = $this->findPages();

        if (isset($options['noRun']) && true === $options['noRun']) {
            return;
        }

        list($status, $content) = $this->getRequestedContent();
        http_response_code($status);
        $this->logAccess();
        print $content;
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

    public function findPages(): array
    {
        $foundPages = [];
        $root = realpath($this->getOption('pagesPath') ?: getcwd() . '/pages');

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

    public function getContentFor(string $slug): array
    {
        $this->setOption('requestUri', '/' . trim($slug, '/'));
        return $this->getRequestedContent();
    }

    protected function buildContentFunction(\SplFileInfo $file)
    {
        switch (strtolower($file->getExtension())) {
            case 'md':
            case 'markdown':
                return function () use ($file) {
                    $contents = file_get_contents($file->getPathName());
                    if (false === $contents) {
                        return '';
                    }
                    return markdown($contents);
                };
            case 'php':
            default:
                return function () use ($file) {
                    ob_start();
                    $required = include $file->getPathName();
                    $output = ob_get_clean();

                    return ($required === 1)
                        ? $output
                        : $required;
                };
        }
    }

    public function getRequestedContent(): array
    {
        $rawUri =
            isset($_SERVER[static::SERVER_REQUEST_URI])
            ? $_SERVER[static::SERVER_REQUEST_URI]
            : $this->getOption('requestUri');
        $requestUri = trim($rawUri, '/');

        if (isset($this->pages[$requestUri])) {
            return [200, $this->pages[$requestUri]->content()];
        }

        if ($requestUri === '' && isset($this->pages['index'])) {
            return [200, $this->pages['index']->content()];
        }

        return [404, 'not found'];
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
