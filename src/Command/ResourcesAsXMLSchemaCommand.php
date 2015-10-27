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

class ResourcesAsXMLSchemaCommand extends Command
{
    protected function configure()
    {
        $this->setName('resources-as-xml-schema');
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

        $output->writeln('<?xml version="1.0" encoding="UTF-8"?>');
        $output->writeln('<database name="default" defaultIdMethod="native">');

        foreach ($resources as $resource)
        {

            $output->writeln('');
            $this->outputResource($input, $output, $resource);
            $output->writeln('');

        }

        $output->writeln('</database>');
    }

    protected function outputResource(InputInterface $input, OutputInterface $output, $resource)
    {
        $output->writeln(sprintf("  <!-- Resource: %s -->", $resource->getResourceID()));
        $output->writeln(sprintf('  <table name="%s" phpName="%s">',
            NameTransform::decamelize($resource->getResourceID()),
            $resource->getResourceID()
        ));

        foreach( $resource->getClasses() as $class )
        {
            $output->writeln('');
            $output->writeln(sprintf('    <!-- Class: %s -->', $class->getClassName()));

            foreach( $this->rets->getTableMetadata($resource->getResourceID(), $class->getClassName()) as $field) {
                $this->outputField($input, $output, $field);
            }

            $output->writeln(sprintf('    <!-- /Class: %s -->', $class->getClassName()));
            $output->writeln('');
        }

        $output->writeln('  </table>');
        $output->writeln(sprintf("  <!-- /Resource: %s -->", $resource->getResourceID()));
    }

    protected function outputField(InputInterface $input, OutputInterface $output, $field)
    {
        $phpName = $field->getStandardName();
        $name = NameTransform::decamelize($phpName);

        $size = null;

        switch( $field->getDataType() )
        {
            case 'DateTime':
            case 'Date':
                $type = 'timestamp';
                break;

            case 'Character':
                $type = 'varchar';
                $size = $field->getMaximumLength();
                break;

            case 'Numeric':
            case 'Int':
                $type = 'integer';
                break;

            case 'Decimal':
                $type = 'double';
                break;

            default:
                $output->writeln(sprintf('<error>Unknown type: %s</error>', $field->getDataType()));
        }

        if( $size )
        {
            $size = " size=\"$size\"";
        }

        $output->writeln(sprintf('    <column name="%s" phpName="%s" type="%s"%s />', $name, $phpName, $type, $size));
    }
}
