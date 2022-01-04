<?php

namespace xgerhard\nbheaders;

class NBHeaders
{
    protected $user = null;
    protected $channel = null;
    protected $responseUrl = null;

    /**
     * Parse Nightbot headers if they are set
    */
    public function __construct()
    {
        $a = ['responseUrl' => 'Nightbot-Response-Url', 'user' => 'Nightbot-User', 'channel' => 'Nightbot-Channel'];
        $aHeaders = $this->getHeaders();

        foreach ($a as $strKey => $strNightbotKey) {
            if (isset($aHeaders[$strNightbotKey])) {
                $val = $aHeaders[$strNightbotKey];

                if ($strKey == 'responseUrl') {
                    $this->{$strKey} = $val;
                } else {
                    parse_str($val, $aVal);
                    $this->{$strKey} = (object) $aVal;
                }
            }
        }
    }

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
     * Set the "Nightbot-User" header data
     *
     *  @param array|object $user
    */
    public function setUser($user)
    {
        $this->user = is_array($user) ? (object) $user : (is_object($user) ? $user : null);
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
     * Set the "Nightbot-Channel" header data
     *
     *  @param array|object $channel
    */
    public function setChannel($channel)
    {
        $this->channel = is_array($channel) ? (object) $channel : (is_object($channel) ? $channel : null);
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
     * Set the "Nightbot-Response-Url" header data
     *
     *  @param string $responseUrl
    */
    public function setResponseUrl($responseUrl)
    {
        $this->responseUrl = (string) $responseUrl;
    }

    /**
     * Returns if the request is a Nightbot request
     *
     * @return boolean
    */
    public function isNightbotRequest()
    {
        return !! $this->channel;
    }

    /**
     * Returns if the request is timer request
     *
     * @return boolean
    */
    public function isTimer()
    {
        return !! $this->channel && ! $this->user;
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
     * If user is set, get the provider from user object. The provider value from the channel object on Discord is the
     * provider that was used to link Nightbot to Discord. Timer messages have no user info in the headers, grab the
     * channel info instead. Timers don't work on Discord so the user should never be null there.
     *
     * @return string|null
    */
    public function getProvider()
    {
        if ($this->user) {
            return $this->user->provider;
        } elseif ($this->channel) {
            return $this->channel->provider;
        } else {
            return null;
        }
    }

    /**
     * Returns headers of the request
     *
     * @return array
    */
    private function getHeaders()
    {
        if (function_exists('getallheaders')) {
            return getallheaders();
        } else {
            $aHeaders = [];
            foreach ($_SERVER as $strName => $strValue) {
                if (substr($strName, 0, 5) == 'HTTP_') {
                    $aHeaders[
                        str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($strName, 5)))))
                    ] = $strValue;
                }
            }
            return $aHeaders;
        }
    }
}
