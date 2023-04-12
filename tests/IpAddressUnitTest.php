<?php

namespace NRB\Multicast\Tests;

use Buibr\Multicast\Models\MulticastIpAddress;
use PHPUnit\Framework\TestCase;

class IpAddressUnitTest extends TestCase
{
    /** @test */
    public function it_validates_multicast_ip_addresses()
    {
        $ip = '239.0.0.19';
        $mc = new MulticastIpAddress($ip);
        
        $this->assertEquals($ip, (string)$mc);
        $this->assertEquals(239, $mc->getG1());
        $this->assertEquals(0, $mc->getG2());
        $this->assertEquals(0, $mc->getG3());
        $this->assertEquals(19, $mc->getG4());
    }
    
    public function test_increments_properly_through_groups()
    {
        $mc = new MulticastIpAddress('239.0.0.19');
        $mc->increment();
        $this->assertEquals('239.0.0.20', (string)$mc);
    }
    
    
    public function test_decrements_properly_through_groups()
    {
        $mc = new MulticastIpAddress('239.0.0.19');
        $mc->decrement();
        $this->assertEquals('239.0.0.18', (string)$mc);
    }
    
    
    public function test_range_detector()
    {
        $mc = new MulticastIpAddress('239.0.0.19');
        $this->assertTrue( $mc->isLocal() );
        
        $mc = new MulticastIpAddress('224.0.0.19');
        $this->assertFalse( $mc->isLocal() );
        $this->assertFalse( $mc->isGlobal() );
        
        $mc = new MulticastIpAddress('224.0.1.19');
        $this->assertTrue( $mc->isGlobal() );
    }
}
