# nbheaders
Easily parse Nightbot headers. Requires PHP 8.2+.

Install:
------
`composer require xgerhard/nbheaders`

Example:
------
```php
require 'vendor/autoload.php';

use xgerhard\nbheaders\NBHeaders;

$nb = new NBHeaders;

if (! $nb->hasNightbotHeaders() || ! $nb->isNightbotIp($_SERVER['REMOTE_ADDR'])) {
    exit('Not a Nightbot request');
}

echo 'Platform: '. $nb->getProvider()
    .' | Channel: '. $nb->getChannel()->displayName
    .' | User: '. ($nb->getUser()?->displayName ?? 'None')
    .' | Timer: '. ($nb->isTimer() ? 'Yes' : 'No')
    .' | Moderator: '. ($nb->isUserModerator() ? 'Yes' : 'No');
```

Available functions:
------
| Function | Returns |
|---|---|
| `getUser()` | The user who called the command, `null` on timers |
| `getChannel()` | The channel where the command was used |
| `getResponseUrl()` | The response url, `null` if it isn't a Nightbot API url |
| `getProvider()` | The provider, e.g. twitch, youtube, discord |
| `hasNightbotHeaders()` | If the request has valid Nightbot headers |
| `isNightbotIp($ip)` | If the IP belongs to Nightbot |
| `isTimer()` | If the command was called by a timer |
| `isUserModerator()` | If the user is a moderator or the owner |

A user has `name`, `displayName`, `provider`, `providerId` and `userLevel`. A channel has the same, without `userLevel`.

Testing:
------
Set your own data to test without Nightbot:

```php
$nb->setChannel(['name' => 'xgerhard', 'displayName' => 'xgerhard', 'provider' => 'twitch', 'providerId' => '1']);
$nb->setUser(['name' => 'bob', 'displayName' => 'Bob', 'provider' => 'twitch', 'providerId' => '2', 'userLevel' => 'everyone']);
$nb->setResponseUrl('https://api.nightbot.tv/1/channel/send/test');
```

