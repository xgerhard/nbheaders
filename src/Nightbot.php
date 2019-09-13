<?php

namespace xgerhard\nbheaders;

class Nightbot
{
    protected $user = null;
    protected $channel = null;
    protected $responseUrl = null;

    /**
     * Returns the "Nightbot-User" header data
     *
     * @return object|null
    */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Returns the "Nightbot-Channel" header data
     *
     * @return object|null
    */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * Returns the "Nightbot-Response-Url" header data
     *
     * @return string|null
    */
    public function getResponseUrl()
    {
        return $this->responseUrl;
    }

    /**
     * Returns if the request is a Nightbot request
     *
     * @return boolean
    */
    public function isNightbotRequest()
    {
        return !!$this->channel;
    }

    /**
     * Returns if the request is timer request
     *
     * @return boolean
    */
    public function isTimer()
    {
        return !!$this->channel && !$this->user;
    }

    /**
     * Returns if the user is a moderator (Owner counts as moderator)
     *
     * @return boolean
    */
    public function isUserModerator()
    {
        return $this->user && ($this->user->userLevel == 'owner' || $this->user->userLevel == 'moderator');
    }

    /**
     * Returns the provider of the Nightbot request
     *
     * If user is set, get the provider from user array, since the channel array will have provider Twitch on Discord.
     * Timer messages on Twitch have no user info in the header, grab the channel info instead. Timers don't work on Discord so the user should never be null there.
     *
     * @return string|null
    */
    public function getProvider()
    {
        if ($this->user)
            return $this->user->provider;
        elseif ($this->channel)
            return $this->channel->provider;
        else
            return null;
    }

    /**
     * Parse Nightbot headers if they are set
     *
     * @param Request $request The request for Laravel applications (optional)
    */
    public function __construct($request = null)
    {
        $a = ['responseUrl' => 'Nightbot-Response-Url', 'user' => 'Nightbot-User', 'channel' => 'Nightbot-Channel'];
        if (!$request)
            $aHeaders = $this->getHeaders();

        foreach ($a as $strKey => $strNightbotKey)
        {
            $val = null;
            if (!$request)
            {
                if (isset($aHeaders[$strNightbotKey]))
                    $val = $aHeaders[$strNightbotKey];
            }
            else $val = $request->header($strNightbotKey);

            if ($val)
            {
                if ($strKey == 'responseUrl')
                    $this->{$strKey} = $val;
                else
                {
                    parse_str($val, $aVal);
                    $this->{$strKey} = (object) $aVal;
                }
            }
        }
    }

    /**
     * Returns headers of the request
     *
     * @return array
    */
    private function getHeaders()
    {
        if (function_exists('getallheaders'))
            return getallheaders();
        else
        {
            $aHeaders = [];
            foreach ($_SERVER as $strName => $strValue)
            {
                if (substr($strName, 0, 5) == 'HTTP_')
                    $aHeaders[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($strName, 5)))))] = $strValue;
            }
            return $aHeaders;
        }
    }
}
?>