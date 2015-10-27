<?php

namespace RetsTools\Util;
use Symfony\Component\Yaml\Parser;

class ConfigLoader
{
    protected static $PATH = __DIR__.'/../../';

    public static function get($configFilename = 'rets.yml')
    {
        $parser = new Parser;
        $configFileContents = file_get_contents(self::$PATH.$configFilename);

        return $parser->parse($configFileContents)['rets'];
    }
}
