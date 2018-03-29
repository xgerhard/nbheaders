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
     * @return array|null
    */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Returns the "Nightbot-Channel" header data
     *
     * @return array|null
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
        return $this->channel != null;
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
        if ($this->user != null)
        {
            return $this->user['provider'];
        }
        elseif ($this->channel != null)
        {
            return $this->channel['provider'];
        }
        return null;
    }

    /**
     * Parse Nightbot headers if they are set
     *
     * @param Request $request The request for Laravel applications (optional)
    */
    public function __construct ($request = null)
    {
        $a = array('responseUrl' => 'Nightbot-Response-Url', 'user' => 'Nightbot-User', 'channel' => 'Nightbot-Channel');
        if ($request === null) $aHeaders = $this->getHeaders();

        foreach ($a AS $strKey => $strNightbotKey)
        {
            $val = null;
            if ($request === null)
            {
                if (isset($aHeaders[$strNightbotKey]))
                    $val = $aHeaders[$strNightbotKey];
            }
            elseif ($request->has($strNightbotKey))
                $val = $request->input($strNightbotKey);

            if ($val)
            {
                if ($strKey == 'responseUrl')
                    $this->{$strKey} = $val;
                else
                    parse_str($val, $this->{$strKey});
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