# nbheaders
Easily handle Nightbot headers 

Example:
------
```php
require 'vendor/autoload.php';

$oNightbot = new xgerhard\nbheaders\Nightbot;

if($oNightbot->isNightbotRequest())
    echo 'Platform: '. $oNightbot->getProvider() .' | User: '. http_build_query($oNightbot->getUser(), '', ', ') .' | Channel: '. http_build_query($oNightbot->getChannel(), '', ', ');
else
    echo 'Not a Nightbot request';
```
Example above can be tested here:
-----
```!commands add !nb $(urlfetch https://2g.be/twitch/Nightbot/headers.php)```

To use, add the following to your composer.json:
------
```
{
    "require": {
        "xgerhard/nbheaders": "dev-master"
    },
    "repositories": [
        {
            "type": "git",
            "url": "https://github.com/xgerhard/nbheaders.git"
        }
    ]
}
```
