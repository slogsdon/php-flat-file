<?php declare(strict_types=1);

namespace FlatFile\Command;

use FlatFile\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BuildCommand extends Command
{
    /** @var InputInterface */
    protected $input;
    /** @var OutputInterface */
    protected $output;
    /** @var Application */
    protected $app;
    /** @var array */
    protected $pages;

    protected function configure()
    {
        $this
            ->setName('build')
            ->setDescription('Builds the site')
            ->addArgument('destination', InputArgument::OPTIONAL, 'Directory to save build files')
            ->addOption(
                'destination',
                'd',
                InputOption::VALUE_REQUIRED,
                'Directory to save build files',
                'dist'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->app = new Application(['noRun' => true]);

        $this
            ->discoverRoutes()
            ->generateFiles();

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

        $destination = sprintf(
            '%s/%s',
            getcwd(),
            $this->getDestination()
        ) . '/';

        if (!is_dir($destination)) {
            mkdir($destination, 0777, true);
        }

        $destination = realpath($destination);


        foreach ($this->pages as $page) {
            if ($page['slug'] === 'index') {
                $page['slug'] = '';
            }

            $this->app->setOption('requestUri', '/' . $page['slug']);
            list(,$content) = $this->app->getContent();
            $localDest = sprintf(
                '%s/%s',
                $destination,
                $page['slug']
            );

            if (!is_dir($localDest)) {
                mkdir($localDest, 0777, true);
            }

            file_put_contents($localDest . '/index.html', $content);
        }
    }

    protected function getDestination()
    {
        return $this->input->getArgument('destination') ?: $this->input->getOption('destination');
    }
}
