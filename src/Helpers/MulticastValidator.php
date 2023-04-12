<?php

namespace Buibr\Multicast\Helpers;

use Buibr\Multicast\Exceptions\InvalidIpAddressException;
use Buibr\Multicast\Exceptions\InvalidMulticastProtocolException;
use Buibr\Multicast\Exceptions\InvalidMulticastRangeException;
use Buibr\Multicast\Exceptions\MulticastIpException;
use Buibr\Multicast\Models\MulticastUri;
use Buibr\Multicast\Multicast;

class MulticastValidator
{
    public static \Throwable $exception;
    
    public static function validate(string $ip): bool
    {
        try {
            self::validateUrl($ip);
            
            $uri = Multicast::create($ip);
            
            self::validateRange($uri);
            
            self::validateProtocol($uri);
            
            return true;
        } catch (MulticastIpException $e) {
            self::$exception = $e;
            return false;
        }
    }
    
    public static function validateRange(MulticastUri $uri): void
    {
        $ip = $uri->getIp();
        
        if (!MulticastRange::inRange(0, $ip->getG1())) {
            throw new InvalidMulticastRangeException("Invalid range in group 1 ");
        }
        
        if (!MulticastRange::inRange(1, $ip->getG2())) {
            throw new InvalidMulticastRangeException("Invalid range in group 2 ");
        }
        
        if (!MulticastRange::inRange(2, $ip->getG3())) {
            throw new InvalidMulticastRangeException("Invalid range in group 2 ");
        }
        
        if (!MulticastRange::inRange(3, $ip->getG4())) {
            throw new InvalidMulticastRangeException("Invalid range in group 2 ");
        }
    }
    
    public static function validateProtocol(MulticastUri $multicastUri): void
    {
        if (!in_array($multicastUri->getScheme(), ['udp', 'rtp', 'rsvp', 'mdns', null])) {
            throw new InvalidMulticastProtocolException("Invalid protocol [{$multicastUri->getScheme()}].");
        }
    }
    
    public static function validateUrl(string $url): bool
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return true;
        }
        
        if (filter_var($url, FILTER_VALIDATE_IP)) {
            return true;
        }
        
        throw new InvalidIpAddressException('Invalid multicast address.');
    }
    
}
