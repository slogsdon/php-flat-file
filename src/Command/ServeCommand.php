<?php declare(strict_types=1);

namespace FlatFile\Command;

use FlatFile\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ServeCommand extends BaseCommand
{
    /** @var string */
    protected $name = 'serve';
    /** @var string */
    protected $description = 'Serves the site';

    protected function configure()
    {
        parent::configure();

        $this
            ->addOption(
                'host',
                'H',
                InputOption::VALUE_REQUIRED,
                'The listening host',
                'localhost'
            )
            ->addOption(
                'port',
                'P',
                InputOption::VALUE_REQUIRED,
                'The listening port',
                '3000'
            )
            ->addOption(
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                'Does not run server. Outputs command instead'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $command = $this->buildCommand();

        if ($this->input->getOption('dry-run')) {
            $this->output->writeln($command);
            return;
        }

        $this->outputBanner();
        passthru($command);
    }

    protected function buildCommand(): string
    {
        $root = '';
        $ini = '';

        if (is_dir($this->getRoot())) {
            $root = '-t ' . $this->getRoot();
        }

        if ($this->input->getOption('quiet')) {
            $ini .= ' -d error_log=0';
        }

        return sprintf(
            '%s%s -S %s:%s %s %s',
            $this->getPhp(),
            $ini,
            $this->getHost(),
            $this->getPort(),
            $root,
            $this->getRouter()
        );
    }

    protected function getPhp(): string
    {
        return PHP_BINARY;
    }

    protected function getHost(): string
    {
        return $this->input->getOption('host');
    }

    protected function getPort(): string
    {
        return $this->input->getOption('port');
    }

    protected function getRoot(): string
    {
        return sprintf('%s/public', getcwd());
    }

    protected function getRouter(): string
    {
        return Application::resolveRouter();
    }

    protected function outputBanner()
    {
        $dt = date(Application::DATETIME_FORMAT);
        $this->output->writeln(sprintf(
            'PHP %s Development Server started at %s',
            PHP_VERSION,
            $dt
        ));
        $this->output->writeln(sprintf(
            'Listening on http://%s:%s',
            $this->getHost(),
            $this->getPort()
        ));
        $this->output->writeln(sprintf('Document root is %s', $this->getRoot()));
        $this->output->writeln('Press Ctrl-C to quit.');
    }
}
