*GetClientIp is a lightweight PHP class for get real/original client IP address, without proxy as opera mini and other.
It uses the specific $_SERVER headers to detect client ip address. Class search valid IPv4 of client*

## Composer install

```
composer require worm/getclientiplib
```

```json
{
    "require": {
        "worm/getclientiplib": "^1.0"
    }
}
```

## Usage

```php
$getClientIp = new GetClientIp;
$ip = $getClientIp->getClientIp();
$longIp = $getClientIp->getLongClientIp();
```

## Usage with manual data

```php
$getClientIp = new GetClientIp(array( "REMOTE_ADDR"           => "1.2.3.4",
                                      "REMOTE_PORT"           => "",
                                      "SERVER_ADDR"           => "1.1.1.1",
                                      "X_FORWARDED_FOR"       => "2.3.4.5,1.2.3.4, 1.2.3.4" ));
$ip = $getClientIp->getClientIp();
$longIp = $getClientIp->getLongClientIp();
```