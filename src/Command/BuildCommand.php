<?php declare(strict_types=1);

namespace FlatFile\Command;

use FlatFile\Application;
use FlatFile\Files;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BuildCommand extends BaseCommand
{
    /** @var string */
    const OPTION_DESTINATION = 'destination';

    /** @var string */
    protected $name = 'build';
    /** @var string */
    protected $description = 'Builds the site';

    /** @var Application */
    protected $app;
    /** @var array */
    protected $pages;

    protected function configure()
    {
        parent::configure();

        $this
            ->addArgument(static::OPTION_DESTINATION, InputArgument::OPTIONAL, 'Directory to save build files')
            ->addOption(
                static::OPTION_DESTINATION,
                'd',
                InputOption::VALUE_REQUIRED,
                'Directory to save build files',
                'dist'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $this->app = new Application(['noRun' => true]);

        $this
            ->discoverRoutes()
            ->generateFiles()
            ->copyStaticAssets();

        $this->output->writeln('Finished');
    }

    protected function discoverRoutes()
    {
        $this->output->writeln('Discovering routes...');
        $this->pages = $this->app->findPages();
        return $this;
    }

    protected function generateFiles()
    {
        $this->output->writeln('Generating files...');

        foreach ($this->pages as $page) {
            if ($page->slug === 'index') {
                $page->slug = '';
            }

            list(,$content) = $this->app->getContentFor($page->slug);
            $localDest = sprintf(
                '%s/%s',
                $this->getDestination(),
                $page->slug
            );

            if (!is_dir($localDest)) {
                mkdir($localDest, 0777, true);
            }

            file_put_contents($localDest . '/index.html', $content);
        }

        return $this;
    }

    protected function copyStaticAssets()
    {
        $public = getcwd() . '/public';
        if (is_dir($public)) {
            $this->output->writeln('Copying public files...');

            $files = new Files;
            foreach ($files->findAll($public) as $file) {
                $path = str_replace($public, '', $file->getPathName());
                copy($file->getPathName(), $this->getDestination() . $path);
            }
        }

        return $this;
    }

    protected function getDestination()
    {
        $destination = sprintf(
            '%s/%s',
            getcwd(),
            $this->input->getArgument(static::OPTION_DESTINATION) ?: $this->input->getOption(static::OPTION_DESTINATION)
        ) . '/';

        if (!is_dir($destination)) {
            mkdir($destination, 0777, true);
        }

        return realpath($destination);
    }
}
