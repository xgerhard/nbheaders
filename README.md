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

// Available functions:
// getUser() retrieve user info who called the command
// getChannel() retrieve channel info where the command was used
// getResponseUrl() retrieve response url to use with Nightbot API
// getProvider() retrieve the provider e.g. twitch, youtube, discord
// isNightbotRequest() checks if the command was called by Nightbot
// isTimer() checks if the command is called from a timer (Returns false if its called by a user)
// isUserModerator() checks if the user calling the command is a moderator
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
