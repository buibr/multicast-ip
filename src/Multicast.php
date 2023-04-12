<?php

namespace Buibr\Multicast;

use Buibr\Multicast\Helpers\MulticastParser;
use Buibr\Multicast\Helpers\MulticastRange as MR;
use Buibr\Multicast\Helpers\MulticastValidator;
use Buibr\Multicast\Models\MulticastUri;

class Multicast
{
    public static function isValidMulticastIP(string $ip): bool
    {
        return MulticastValidator::validate($ip);
    }
    
    public static function validate(string $uri)
    {
        if(!MulticastValidator::validate($uri)){
            throw MulticastValidator::$exception;
        }
    }
    
    public static function random()
    {
        return self::create(
            MR::getRandom(0) . "." . MR::getRandom(1) . "." . MR::getRandom(2) . "." . MR::getRandom(3)
        );
    }
    
    public static function create(string $uri): MulticastUri
    {
        $parse = MulticastParser::parse($uri);
        
        $multicast = new MulticastUri;
        
        $multicast->setIp($parse['ip']);
        $multicast->setPort($parse['port']);
        $multicast->setQuery($parse['query']);
        $multicast->setScheme($parse['scheme']);
        
        return $multicast;
    }
}
