<?php

namespace RetsTools\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use RetsTools\Util\ConfigLoader;
use RetsTools\Util\NameTransform;
use PHRETS\Session;

class ResourcesAsMapCommand extends Command
{
    protected function configure()
    {
        $this->setName('resources-as-map');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if( !$this->connect($input, $output) ) {
            return false;
        }

        $this->outputXmlSchema($input, $output);
    }

    protected function connect(InputInterface $input, OutputInterface $output)
    {
        $config = ConfigLoader::get();

        $this->rets = new Session($config);

        try {
            $this->connect = $this->rets->Login();
        }
        catch(Exception $e) {
            $output->writeln('<error>Cannot connect:</error>');
            $output->writeln("<error>$e</error>");

            return false;
        }

        return true;
    }

    protected function outputXmlSchema(InputInterface $input, OutputInterface $output)
    {
        $system = $this->rets->GetSystemMetadata();
        $resources = $system->getResources();

        foreach ($resources as $resource)
        {

            $output->writeln('');
            $this->outputResource($input, $output, $resource);
            $output->writeln('');

        }
    }

    protected function outputResource(InputInterface $input, OutputInterface $output, $resource)
    {
        $output->writeln(sprintf("// Resource: %s", $resource->getResourceID()));
        $output->writeln(sprintf('$map = [',
            NameTransform::decamelize($resource->getResourceID()),
            $resource->getResourceID()
        ));

        foreach( $resource->getClasses() as $class )
        {
            $output->writeln('');
            $output->writeln(sprintf('    \'%s\' => [', $class->getClassName()));

            foreach( $this->rets->getTableMetadata($resource->getResourceID(), $class->getClassName()) as $field) {
                $output->writeln(sprintf('          \'%s\',', $field->getStandardName()));
            }

            $output->writeln(sprintf('    ],', $class->getClassName()));
            $output->writeln('');
        }

        $output->writeln('];');
        $output->writeln(sprintf("// End Resource %s", $resource->getResourceID()));
    }
}
