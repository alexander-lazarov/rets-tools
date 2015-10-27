<?php

namespace RetsTools\Util;
use Symfony\Component\Yaml\Parser;
use PHRETS\Configuration;

class ConfigLoader
{
    protected static $PATH = __DIR__.'/../../';

    protected static function getRawConfig($configFilename)
    {
        $parser = new Parser;
        $configFileContents = file_get_contents(self::$PATH.$configFilename);

        return $parser->parse($configFileContents)['rets'];
    }

    public static function get($configFilename = 'rets.yml')
    {
        $c = self::getRawConfig($configFilename);
        $retsConfig = new Configuration;

        $retsConfig->setLoginUrl($c['url'])
                   ->setUsername($c['username'])
                   ->setPassword($c['password'])
                   ->setRetsVersion($c['rets_version'])
                   ->setUserAgent($c['user_agent'])
                   ->setUserAgentPassword($c['user_agent_password'])
                   ->setHttpAuthenticationMethod($c['http_auth_method'])
                   ->setOption('use_post_method', $c['use_post_method'])
                   ->setOption('disable_follow_location', $c['disable_follow_location']);

        return $retsConfig;
    }
}
