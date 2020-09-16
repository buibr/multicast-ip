<?php


namespace NRB\Multicast\Tests;

use NRB\Multicast\Exceptions\InvalidIpAddressException;
use NRB\Multicast\Exceptions\InvalidMulticastProtocolException;
use NRB\Multicast\Exceptions\InvalidMulticastRangeException;
use NRB\Multicast\MulticastIP;
use PHPUnit\Framework\TestCase;

class MulticastIpUnitTest extends TestCase
{
    /** @test */
    public function it_validates_ip_addresses()
    {
        $ip = '239.0.0.1';
        $mc = new MulticastIP( $ip );
        
        $this->assertEquals( $ip, (string)$mc );
    }
    
    /** @test */
    public function it_validates_full_multicast()
    {
        $ip = 'udp://239.0.0.1:12345?test=12343';
        $mc = new MulticastIP( $ip );
        
        $this->assertEquals( $ip, (string)$mc );
    
        $ip = 'rtp://239.0.0.1:12345?test=12343';
        $mc = new MulticastIP( $ip );
    
        $this->assertEquals( $ip, (string)$mc );
    }
    
    /** @test */
    public function it_throws_invalid_ip_exception()
    {
        /** when port is more than 5 charcters */
        $this->expectException(InvalidIpAddressException::class);
        
        $mc = new MulticastIP('udp://240.0.0.1:1234235');
        $mc = new MulticastIP('adfa');
    }
    
    /** @test */
    public function it_throws_invalid_protocol_exception()
    {
        $this->expectException(InvalidMulticastProtocolException::class);
        $mc = new MulticastIP('http://239.0.0.1:12345?alo=12343');
    }
    
    /** @test */
    public function it_throws_invalid_range_exception()
    {
        $this->expectException(InvalidMulticastRangeException::class);
        $mc = new MulticastIP('udp://240.0.0.1:12345');
    
        $this->expectException(InvalidMulticastRangeException::class);
        $mc = new MulticastIP('udp://220.0.0.1:12345');
    }
    
    /** @test */
    public function it_adds_ip_correct()
    {
        $mc = new MulticastIP('udp://239.0.0.1:12345');
        
        $this->assertEquals('udp://239.0.0.2:12345', (string) $mc->add() );
        $this->assertEquals('udp://239.0.0.3:12345', (string) $mc->add() );
    
        // third range group.
        $mc = new MulticastIP('udp://239.0.20.255:12345');
        $this->assertEquals('udp://239.0.21.1:12345', (string) $mc->add() );
        
        // second range group.
        $mc = new MulticastIP('udp://239.10.255.255:12345');
        $this->assertEquals('udp://239.11.0.1:12345', (string) $mc->add() );
        
        // first range test
        $mc = new MulticastIP('udp://238.255.255.255:12345');
        $this->assertEquals('udp://239.0.0.1:12345', (string) $mc->add() );
    }
    
    /** @test */
    public function it_subs_ip_correct()
    {
        $mc = new MulticastIP('udp://239.0.0.3:12345');
        
        $this->assertEquals('udp://239.0.0.2:12345', (string) $mc->sub() );
        $this->assertEquals('udp://239.0.0.1:12345', (string) $mc->sub() );
        
        // thirt range group
        $mc = new MulticastIP('udp://239.0.20.1:12345');
        $this->assertEquals('udp://239.0.19.255:12345', (string) $mc->sub() );
        
        // second range group
        $mc = new MulticastIP('udp://239.10.0.1:12345');
        $this->assertEquals('udp://239.9.255.255:12345', (string) $mc->sub() );
    
        // first range group
        $mc = new MulticastIP('udp://225.0.0.1:12345');
        $this->assertEquals('udp://224.255.255.255:12345', (string) $mc->sub() );
    }
    
    
    /** @test */
    public function it_validates_correct_from_static()
    {
        $this->assertTrue( MulticastIP::isValid('udp://225.0.0.1:12345') );
        $this->assertTrue( MulticastIP::isValid('rtp://225.0.0.1:12345') );
        
        $this->assertFalse( MulticastIP::isValid('udp://225.0.0.1:12345r') );
        $this->assertFalse( MulticastIP::isValid('udp://localhost:1234') );
        $this->assertFalse( MulticastIP::isValid('http://225.0.0.1:12345') );
    }
    
    /** @test */
    public function it_does_well_getters()
    {
        $mc = new MulticastIP('udp://239.0.0.1');
        
        $this->assertTrue( $mc->validRange() );
    
        $this->assertEquals('239.0.0.1', $mc->getIp() );
        $this->assertEquals('udp', $mc->getScheme() );
        $this->assertNull( $mc->getPort() );
        $this->assertNull( $mc->getQuery() );
    }
    
}