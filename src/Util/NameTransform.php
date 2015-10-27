<?php

namespace RetsTools\Util;

class NameTransform
{
    // credits: code taken from http://goo.gl/dDxRmw

    public static function decamelize($word)
    {
        return $word = strtolower(preg_replace_callback(
            "/(^|[a-z])([A-Z])/",
            function($m) { return strtolower(strlen($m[1]) ? "$m[1]_$m[2]" : "$m[2]"); },
            $word
        ));
    }

    function camelize($word)
    {
        return $word = preg_replace_callback(
            "/(^|_)([a-z])/",
            function($m) { return strtoupper("$m[2]"); },
            $word
        );
    }
}
