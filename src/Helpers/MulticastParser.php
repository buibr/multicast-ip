<?php

namespace Buibr\Multicast\Helpers;

class MulticastParser
{
    public static function parse(string $ip)
    {
        if (filter_var($ip, FILTER_VALIDATE_URL)) {
            return self::parseFromUrl($ip);
        }
        
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return self::parseFromIp($ip);
        }
        
        return false;
    }
    
    protected static function parseFromUrl($ip): array
    {
        $ex = parse_url($ip);
        
        return [
            'ip'     => $ex['host'] ?? null,
            'scheme' => $ex['scheme'] ?? null,
            'port'   => $ex['port'] ?? null,
            'query'  => $ex['query'] ?? null,
        ];
    }
    
    protected static function parseFromIp(string $ip): array
    {
        $ex = parse_url($ip);
        
        return [
            'ip'     => $ex['path'] ?? '',
            'scheme' => null,
            'port'   => null,
            'query'  => null,
        ];
    }
}
