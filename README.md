# Mlticast IP
This package provides a set of functions for manipulating and validating Multicast IP addresses. Multicast IP addresses are used for communication between multiple hosts on a network. This package helps in validating and manipulating the Multicast IP addresses in various formats.

### Installation
You can install this package via composer using the following command:
```bash
composer require buibr/multicast-ip
```

### Usage

#### Validating Multicast IP addresses
You can use the isValidMulticastIP function to check if a given IP address is a valid Multicast IP address. It returns true if the IP address is valid, otherwise false.

```php
use Buibr\Multicast\Multicast;

$ipAddress = '239.255.0.1';

if (Multicast::isValidMulticastIP($ipAddress)) {
    echo "Valid Multicast IP";
}
```

#### Add / Substract

```php
use Buibr\Multicast\Multicast;

$ip = Multicast::create('udp://239.0.0.10:12345');

$ip->add(); 
//or
$ip->getIp()->increment();
print (string)$mc // 'udp://239.0.0.11:12345', 

$ip->sub();
or
$ip->getIp()->decrement();
print (string)$mc // 'udp://239.0.0.9:12345',

```

#### Range group detector

```php
use Buibr\Multicast\Multicast;

$url = Multicast::create('udp://239.0.0.1:12345');
$url->getIp()->isLocal(); // true 

$url = Multicast::create('udp://224.0.1.1:12345');
$url->getIp()->isGlobal(); // true
```
