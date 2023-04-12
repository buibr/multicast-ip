<?php

namespace Buibr\Multicast\Models;

use Buibr\Multicast\Helpers\MulticastValidator;

class MulticastUri
{
    private MulticastIpAddress $ip;
    private ?int $port;
    private ?string $scheme;
    private ?string $query;
    
    public function getIp()
    {
        return $this->ip;
    }
    
    public function setIp(string $ip)
    {
        $this->ip = new MulticastIpAddress($ip);
        
        MulticastValidator::validateRange($this);
        
        return $this;
    }
    
    public function getPort(): ?int
    {
        return $this->port;
    }
    
    public function setPort(?int $port = null)
    {
        $this->port = $port;
        return $this;
    }
    
    public function getScheme(): ?string
    {
        return $this->scheme;
    }
    
    public function setScheme(?string $scheme = null)
    {
        $this->scheme = $scheme;
        
        MulticastValidator::validateProtocol($this);
        
        return $this;
    }
    
    public function getQuery(): ?string
    {
        return $this->query;
    }
    
    public function setQuery(?string $query = null): self
    {
        $this->query = $query;
        return $this;
    }
    
    /**
     * @return string
     */
    public function __toString()
    {
        $url = $this->scheme ? $this->scheme . '://' : "";
        $url .= (string)$this->ip;
        
        if ($this->port) {
            $url .= ':' . $this->port;
        }
        
        if ($this->query) {
            $url .= '?' . $this->query;
        }
        
        return $url;
    }
    
    public function add()
    {
        $this->ip->increment();
        return $this;
    }
    
    public function sub($check = null)
    {
        $this->ip->decrement();
        return $this;
    }
}
