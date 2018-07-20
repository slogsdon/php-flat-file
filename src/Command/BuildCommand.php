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

        $app = new Application(['noRun' => true]);
        $destination = sprintf(
            '%s/%s',
            getcwd(),
            $this->getDestination()
        ) . '/';

        if (!is_dir($destination)) {
            mkdir($destination, 0777, true);
        }

        $destination = realpath($destination);

        $this->output->writeln('Discovering routes...');
        $pages = $app->findPages();
        $this->output->writeln('Writing files...');

        foreach ($pages as $page) {
            if ($page['slug'] === 'index') {
                $page['slug'] = '';
            }

            $app->setOption('requestUri', '/' . $page['slug']);
            list(,$content) = $app->getContent();
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

        $this->output->writeln('Finished');
    }

    protected function getDestination()
    {
        return $this->input->getArgument('destination') ?: $this->input->getOption('destination');
    }
}
