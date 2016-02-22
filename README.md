*Get_Client_Ip is a lightweight PHP class for get real/original client IP address, without proxy as opera mini and other.
It uses the specific $_SERVER headers to detect client ip address.*

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
$ip = Get_Client_Ip::getClientIp();
```
