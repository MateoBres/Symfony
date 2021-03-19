<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class SinervisScriptManager extends Command
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * SinervisCronUpdateEditions constructor.
     * @param EntityManagerInterface $em
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManagerInterface $em, LoggerInterface $logger)
    {
        parent::__construct();
        $this->em = $em;
        $this->logger = $logger;
    }

    protected function configure()
    {
        $this
            ->setHelp('This command execute a script. use --script=<cronName> and --silent to disable messages')
            ->addOption('silent', 's', InputOption::VALUE_NONE, 'Silent execution')
            ->addOption('script', null, InputOption::VALUE_REQUIRED, 'Which script you want to execute?');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        define('SILENT', $input->getOption('silent'));
        define('SCRIPT', trim($input->getOption('script')));

        if (strtolower(SCRIPT) === 'all') {
            foreach (get_class_methods($this) as $script) {
                if (substr($script, 0, 4) === 'cron') {
                    $this->writeScriptHeader(ucfirst(substr($script, 4)), $output);
                    $this->logger->info('Executing ' . ucfirst(substr($script, 4)));
                    $this->{$script}($input, $output);
                }
            }
        } else if (method_exists($this, 'cron' . ucfirst(SCRIPT))) {
            $this->writeScriptHeader(SCRIPT, $output);
            $this->logger->info('Executing ' . ucfirst(SCRIPT));
            $this->{'cron' . ucfirst(SCRIPT)}($input, $output);
        } else {
            $output->writeln('<error>This script doesn\'t exist.</error>');
            $this->logger->error('This script doesn\'t exist: ' . ucfirst(SCRIPT));
        }

        $this->em->flush();
    }

    protected function writeScriptHeader($script, OutputInterface $output)
    {
        if (!SILENT) {
            $output->writeln([
                '',
                '**** Executing ' . $script . ' ****',
                '',
            ]);
        }
    }
}