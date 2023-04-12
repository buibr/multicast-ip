<?php

namespace Buibr\Multicast\Models;

use Buibr\Multicast\Exceptions\InvalidIpAddressException;
use Buibr\Multicast\Exceptions\InvalidMulticastRangeException;
use Buibr\Multicast\Helpers\MulticastRange;

class MulticastIpAddress
{
    private string $g1;
    private string $g2;
    private string $g3;
    private string $g4;
    
    public function __construct(string $ip)
    {
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return [$this->g1, $this->g2, $this->g3, $this->g4] = explode('.', $ip);
        }
        
        throw new InvalidIpAddressException('This IP is not valid');
    }
    
    public function __toString()
    {
        return "{$this->g1}.{$this->g2}.{$this->g3}.{$this->g4}";
    }
    
    /**
     * @return string
     */
    public function getG1(): string
    {
        return $this->g1;
    }
    
    /**
     * @return string
     */
    public function getG2(): string
    {
        return $this->g2;
    }
    
    /**
     * @return string
     */
    public function getG3(): string
    {
        return $this->g3;
    }
    
    /**
     * @return string
     */
    public function getG4(): string
    {
        return $this->g4;
    }
    
    public function increment() : self
    {
        if($this->g4 < MulticastRange::getMaximal(3)){
            $this->g4++;
            return $this;
        }
        
        if($this->g3 < MulticastRange::getMaximal(2)){
            $this->g3++;
            $this->g4 = 0;
            return $this;
        }
        
        if($this->g2 < MulticastRange::getMaximal(1)){
            $this->g2++;
            $this->g3 = 0;
            $this->g4 = 0;
            return $this;
        }
        
        if($this->g1 < MulticastRange::getMaximal(0)){
            $this->g1++;
            $this->g2 = 0;
            $this->g3 = 0;
            $this->g4 = 0;
            return $this;
        }
        
        throw new InvalidMulticastRangeException('Highest ip range exceeded.');
    }
    
    public function decrement()
    {
        if($this->g4 > MulticastRange::getMinimal(3)){
            $this->g4--;
            return $this;
        }
        
        if($this->g3 > MulticastRange::getMinimal(2)){
            $this->g3--;
            $this->g4 = 255;
            return $this;
        }
        
        if($this->g2 > MulticastRange::getMinimal(1)){
            $this->g2--;
            $this->g3 = 255;
            $this->g4 = 255;
            return $this;
        }
        
        if($this->g1 > MulticastRange::getMinimal(0)){
            $this->g1--;
            $this->g2 = 255;
            $this->g3 = 255;
            $this->g4 = 255;
            return $this;
        }
    }
    
    public function isLocal()
    {
        return MulticastRange::isLocal($this);
    }
    
    public function isGlobal()
    {
        return MulticastRange::isGlobal($this);
    }
}
