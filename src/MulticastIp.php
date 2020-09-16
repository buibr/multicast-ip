<?php


namespace NRB\Multicast;

use NRB\Multicast\Exceptions\InvalidIpAddressException;
use NRB\Multicast\Exceptions\InvalidMulticastProtocolException;
use NRB\Multicast\Exceptions\InvalidMulticastRangeException;

class MulticastIP
{
    /** @var string */
    private $ip;
    
    /** @var integer */
    private $port;
    
    /** @var string */
    private $scheme;
    
    /** @var string */
    private $query;
    
    /**
     *  Min ip range according to Host Extensions for IP Multicasting
     *  https://tools.ietf.org/html/rfc1112
     *
     * @var array<Integer>
     */
    private $min_ip = [224, 0, 0, 1]; // RFC1112 standard
    
    /**
     *  Max ip range according to Host Extensions for IP Multicasting
     *  https://tools.ietf.org/html/rfc1112
     *
     * @var array<Integer>
     */
    private $max_ip = [239, 255, 255, 255];
    
    
    public function __construct(string $range = NULL)
    {
        if ($range) {
            $this->parse($range)->validate();
    
            return;
        }
        
        $this->ip = implode('.', $this->min_ip);
    }
    
    /**
     * @return void
     * @throws \NRB\Multicast\Exceptions\InvalidMulticastProtocolException
     * @throws \NRB\Multicast\Exceptions\InvalidMulticastRangeException
     */
    private function validate()
    {
        $this->validRange();
        
        $this->validProtocol();
        
    }
    
    /**
     * @return bool
     * @throws \NRB\Multicast\Exceptions\InvalidMulticastRangeException
     */
    public function validRange()
    {
        $range = explode('.', $this->ip);
        
        foreach ($range as $k => $v) {
            
            if ($v < $this->min_ip[$k]) {
                throw new InvalidMulticastRangeException("Invalid range {$v} in group {$k} ");
            }
            
            if ($v > $this->max_ip[$k]) {
                throw new InvalidMulticastRangeException("Invalid range {$v} in group {$k} ");
            }
            
        }
        
        return TRUE;
    }
    
    /**
     * @return bool|null
     * @throws \NRB\Multicast\Exceptions\InvalidMulticastProtocolException
     */
    public function validProtocol()
    {
        if (!$this->scheme) {
            return NULL;
        }
        
        if (!\in_array($this->scheme, ['udp', 'rtp', 'rsvp', 'mdns'])):
            throw new InvalidMulticastProtocolException("Invalid protocol [{$this->scheme}].");
        endif;
        
        return TRUE;
    }
    
    /**
     * @param string $ip
     *
     * @return $this
     * @throws \NRB\Multicast\Exceptions\InvalidIpAddressException
     */
    private function parse(string $ip)
    {
        $ex = parse_url($ip);
        
        if (filter_var($ip, FILTER_VALIDATE_URL)) {
            
            if ($ex['scheme'] ?? NULL) {
                $this->ip = $ex['host'] ?? '';
                $this->scheme = $ex['scheme'] ?? '';
                $this->port = $ex['port'] ?? 0;
                $this->query = $ex['query'] ?? '';
            }
            
            return $this;
        }
        
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            
            if ($ex['path'] ?? NULL) {
                $this->ip = $ex['path'] ?? '';
            }
            
            return $this;
        }
        
        throw new InvalidIpAddressException('Invalid multicast address.');
    }
    
    /**
     * @param string $ip
     *
     * @return bool
     */
    public static function isValid(string $ip)
    {
        try {
            new self($ip);
            
            return TRUE;
        } catch (\Exception $e) {
            return FALSE;
        }
    }
    
    /**
     * @return mixed
     */
    public function getIp()
    {
        return $this->ip;
    }
    
    /**
     * @param string $ip
     *
     * @return $this
     * @throws \NRB\Multicast\Exceptions\InvalidMulticastRangeException
     */
    public function setIp(string $ip)
    {
        $this->ip = $ip;
        
        $this->validRange();
        
        return $this;
    }
    
    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }
    
    /**
     * @param int $port
     *
     * @return $this
     * @throws \NRB\Multicast\Exceptions\InvalidMulticastProtocolException
     * @throws \NRB\Multicast\Exceptions\InvalidMulticastRangeException
     */
    public function setPort(int $port)
    {
        $this->port = $port;
        
        $this->validate();
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getScheme()
    {
        return $this->scheme;
    }
    
    /**
     * @param string $scheme
     *
     * @return $this
     * @throws \NRB\Multicast\Exceptions\InvalidMulticastProtocolException
     * @throws \NRB\Multicast\Exceptions\InvalidMulticastRangeException
     */
    public function setScheme(string $scheme)
    {
        $this->scheme = $scheme;
        
        $this->validate();
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }
    
    /**
     * @param string $query
     *
     * @return $this
     */
    public function setQuery(string $query)
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
        $url .= $this->ip;
        
        if ($this->port) {
            $url .= ':' . $this->port;
        }
        
        if ($this->query) {
            $url .= '?' . $this->query;
        }
        
        return $url;
    }
    
    /**
     * @return $this
     * @throws \NRB\Multicast\Exceptions\InvalidMulticastRangeException
     */
    public function add()
    {
        $ip = explode('.', $this->ip);
        
        if ($ip[3] < $this->max_ip[3]) {
            $ip[3]++;
        } elseif ($ip[2] < $this->max_ip[2]) {
            $ip[2]++;
            $ip[3] = $this->min_ip[3];
        } elseif ($ip[1] < $this->max_ip[1]) {
            $ip[1]++;
            $ip[2] = $this->min_ip[2];
            $ip[3] = $this->min_ip[3];
        } elseif ($ip[0] < $this->max_ip[0]) {
            $ip[0]++;
            $ip[1] = $this->min_ip[1];
            $ip[2] = $this->min_ip[2];
            $ip[3] = $this->min_ip[3];
        } else {
            throw new InvalidMulticastRangeException("Highest multicast ip range reached.");
        }
        
        $this->ip = implode('.', $ip);
        
        return $this;
        
    }
    
    /**
     * @param null $check
     *
     * @return $this
     * @throws \NRB\Multicast\Exceptions\InvalidMulticastRangeException
     */
    public function sub($check = NULL)
    {
        $ip = explode('.', $this->ip);
        
        if ($ip[3] > $this->min_ip[3]) {
            $ip[3]--;
        } elseif ($ip[2] > $this->min_ip[2]) {
            $ip[2]--;
            $ip[3] = $this->max_ip[3];
        } elseif ($ip[1] > $this->min_ip[1]) {
            $ip[1]--;
            $ip[2] = $this->max_ip[2];
            $ip[3] = $this->max_ip[3];
        } elseif ($ip[0] > $this->min_ip[0]) {
            $ip[0]--;
            $ip[1] = $this->max_ip[1];
            $ip[2] = $this->max_ip[2];
            $ip[3] = $this->max_ip[3];
        } else {
            throw new InvalidMulticastRangeException("The lowest multicast ip range has been reached.");
        }
        
        $this->ip = implode('.', $ip);
        
        return $this;
    }
}
