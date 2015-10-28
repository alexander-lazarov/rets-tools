<?php

namespace RetsTools\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use RetsTools\Util\ConfigLoader;
use PHRETS\Session;

class TestConnectionCommand extends Command
{
    protected function configure()
    {
        $this->setName('test-connection');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = ConfigLoader::get();

        $output->writeln('<comment>Testing connection...</comment>');
        $rets = new Session($config);

        try
        {
            $connect = $rets->Login();
        }
        catch(Exception $e) {
            $output->writeln('<error>Cannot connect:</error>');
            $output->writeln("<error>$e</error>");

            return;
        }

        $output->writeln('<info>Conntected</info>');
    }
}
