# nbheaders
Easily handle Nightbot headers 

Example:
------
```php
require 'vendor/autoload.php';

$nbheaders = new xgerhard\nbheaders\nbheaders;

if($nbheaders->isNightbotRequest())
    echo 'Platform: '. $nbheaders->getProvider() .' | User: '. http_build_query($nbheaders->getUser(), '', ', ') .' | Channel: '. http_build_query($nbheaders->getChannel(), '', ', ') .' | Timer: '. ($nbheaders->isTimer() ? 'Yes' : 'No') .' | Moderator: '. ( $nbheaders->isUserModerator() ? 'Yes' : 'No');
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
Test example above:
-----
```!commands add !nb $(urlfetch https://2g.be/twitch/Nightbot/headers.php)```

Install nbheaders in your project:
------
`composer require xgerhard/nbheaders`
