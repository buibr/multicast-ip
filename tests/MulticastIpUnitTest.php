<?php

namespace NRB\Multicast\Tests;

use Buibr\Multicast\Exceptions\InvalidIpAddressException;
use Buibr\Multicast\Exceptions\InvalidMulticastProtocolException;
use Buibr\Multicast\Exceptions\InvalidMulticastRangeException;
use Buibr\Multicast\Multicast;
use PHPUnit\Framework\TestCase;

class MulticastIpUnitTest extends TestCase
{
    /** @test */
    public function it_validates_ip_addresses()
    {
        $ip = '239.0.0.1';
        $mc = Multicast::create($ip);
        $this->assertEquals($ip, (string)$mc);
    }
    
    /** @test */
    public function it_validates_full_multicast()
    {
        $ip = 'udp://239.0.0.1:12345?test=12343';
        $mc = Multicast::create($ip);
        $this->assertEquals($ip, (string)$mc);
        
        $ip = 'rtp://239.0.0.1:12345?test=12343';
        $mc = Multicast::create($ip);
        $this->assertEquals($ip, (string)$mc);
    }
    
    /** @test */
    public function it_throws_invalid_ip_exception()
    {
        /** when port is more than 5 charcters */
        $this->expectException(InvalidIpAddressException::class);
        Multicast::validate('adfa');
    }
    
    /** @test */
    public function it_throws_invalid_protocol_exception()
    {
        $this->expectException(InvalidMulticastProtocolException::class);
        Multicast::validate('http://239.0.0.1:12345?alo=12343');
    }
    
    /** @test */
    public function it_throws_invalid_max_range_exception()
    {
        $this->expectException(InvalidMulticastRangeException::class);
        Multicast::validate('udp://240.0.0.1:12345');
    }
    
    /** @test */
    public function it_throws_invalid_min_range_exception()
    {
        $this->expectException(InvalidMulticastRangeException::class);
        Multicast::validate('udp://220.0.0.1:12345');
    }
    
    /** @test */
    public function it_adds_ip_correct()
    {
        $mc = Multicast::create('udp://239.0.0.1:12345');
        
        $this->assertEquals('udp://239.0.0.2:12345', (string)$mc->add());
        $this->assertEquals('udp://239.0.0.3:12345', (string)$mc->add());
        
        // third range group.
        $mc = Multicast::create('udp://239.0.20.255:12345');
        $this->assertEquals('udp://239.0.21.0:12345', (string)$mc->add());
        
        // second range group.
        $mc = Multicast::create('udp://239.10.255.255:12345');
        $this->assertEquals('udp://239.11.0.0:12345', (string)$mc->add());
        
        // first range test
        $mc = Multicast::create('udp://238.255.255.255:12345');
        $this->assertEquals('udp://239.0.0.0:12345', (string)$mc->add());
    }
    
    /** @test */
    public function it_subs_ip_correct()
    {
        $mc = Multicast::create('udp://239.0.0.3:12345');
        
        $this->assertEquals('udp://239.0.0.2:12345', (string)$mc->sub());
        $this->assertEquals('udp://239.0.0.1:12345', (string)$mc->sub());
        
        // thirt range group
        $mc = Multicast::create('udp://239.0.20.0:12345');
        $this->assertEquals('udp://239.0.19.255:12345', (string)$mc->sub());
        
        // second range group
        $mc = Multicast::create('udp://239.10.0.0:12345');
        $this->assertEquals('udp://239.9.255.255:12345', (string)$mc->sub());
        
        // first range group
        $mc = Multicast::create('udp://225.0.0.0:12345');
        $this->assertEquals('udp://224.255.255.255:12345', (string)$mc->sub());
    }
    
    
    /** @test */
    public function it_validates_correct_from_static()
    {
        $is = Multicast::isValidMulticastIP('udp://225.0.0.1:12345');
        
        $this->assertTrue(Multicast::isValidMulticastIP('udp://225.0.0.1:12345'));
        $this->assertTrue(Multicast::isValidMulticastIP('rtp://225.0.0.1:12345'));
        //
        $this->assertFalse(Multicast::isValidMulticastIP('udp://225.0.0.1:12345r'));
        $this->assertFalse(Multicast::isValidMulticastIP('udp://localhost:1234'));
        $this->assertFalse(Multicast::isValidMulticastIP('http://225.0.0.1:12345'));
    }
    
    /** @test */
    public function it_does_well_getters()
    {
        $mc = Multicast::create('udp://239.0.0.1');
        
        $this->assertEquals('239.0.0.1', $mc->getIp());
        $this->assertEquals('udp', $mc->getScheme());
        $this->assertEquals(0, $mc->getPort());
        $this->assertNull($mc->getQuery());
    }
    
}
