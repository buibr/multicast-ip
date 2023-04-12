<?php

namespace Buibr\Multicast\Helpers;

use Buibr\Multicast\Exceptions\InvalidIpGroupException;
use Buibr\Multicast\Models\MulticastIpAddress;

class MulticastRange
{
    protected static array $group1 = ['min' => 224, 'max' => 239];
    
    protected static array $group2 = ['min' => 0, 'max' => 255];
    
    protected static array $group3 = ['min' => 0, 'max' => 255];
    
    protected static array $group4 = ['min' => 0, 'max' => 255];
    
    /**
     * Reserved for special “well-known” multicast addresses
     *
     * @var array|array[]
     */
    protected static array $reserved = ['min' => [224, 0, 0, 0], 'max' => [224, 0, 0, 255]];
    
    /**
     * Globally-scoped (Internet-wide) multicast addresses
     *
     * @var array|array[]
     */
    protected static array $global = ['min' => [224, 0, 1, 0], 'max' => [238, 255, 255, 255]];
    
    /**
     * Administratively-scoped (local) multicast addresses.
     *
     * @var array|array[]
     */
    protected static array $local = ['min' => [239, 0, 0, 0], 'max' => [239, 255, 255, 255]];
    
    
    public static function getMinimal(int $group): ?int
    {
        switch ($group) {
            case 0:
                return self::$group1['min'];
            case 1:
                return self::$group2['min'];
            case 2:
                return self::$group3['min'];
            case 3:
                return self::$group4['min'];
            default:
                throw new InvalidIpGroupException('There is no group greater than 4. You are trying' . $group);
        }
    }
    
    public static function getMaximal(int $group): ?int
    {
        switch ($group) {
            case 0:
                return self::$group1['max'];
            case 1:
                return self::$group2['max'];
            case 2:
                return self::$group3['max'];
            case 3:
                return self::$group4['max'];
            default:
                throw new InvalidIpGroupException('There is no group greater than 4. You are trying' . $group);
        }
    }
    
    public static function getRandom(int $group)
    {
        return random_int(self::getMinimal($group), self::getMaximal($group));
    }
    
    public static function inRange(int $group, int $value)
    {
        return $value <= self::getMaximal($group) && $value >= self::getMinimal($group);
    }
    
    public static function inRangeLocal(int $group, int $value)
    {
        return $value <= self::$local['max'][$group] && $value >= self::$local['min'][$group];
    }
    
    public static function inRangeGlobal(int $group, int $value)
    {
        return $value <= self::$global['max'][$group] && $value >= self::$global['min'][$group];
    }
    
    public static function isLocal(MulticastIpAddress $mip)
    {
        return self::inRangeLocal(0,$mip->getG1());
    }
    
    public static function isGlobal(MulticastIpAddress $mip)
    {
        return self::inRangeGlobal(0,$mip->getG1()) && self::inRangeGlobal(2, $mip->getG3());
    }
}
