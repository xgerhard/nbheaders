<?php

namespace xgerhard\nbheaders;

use InvalidArgumentException;

class NBHeaders
{
    protected ?User $user = null;
    protected ?Channel $channel = null;
    protected ?string $responseUrl = null;

    /**
     * Parse the Nightbot headers of the current request, if they are set
     */
    public function __construct()
    {
        $channelHeader = $_SERVER['HTTP_NIGHTBOT_CHANNEL'] ?? null;
        $userHeader = $_SERVER['HTTP_NIGHTBOT_USER'] ?? null;

        if ($channelHeader === null) {
            return;
        }

        try {
            $channel = Channel::fromArray($this->parse($channelHeader));
            $user = $userHeader !== null ? User::fromArray($this->parse($userHeader)) : null;
        } catch (InvalidArgumentException) {
            // A broken user header must not turn the request into a timer, so reject both.
            return;
        }

        $this->channel = $channel;
        $this->user = $user;
        $this->responseUrl = $_SERVER['HTTP_NIGHTBOT_RESPONSE_URL'] ?? null;
    }

    /**
     * The user who called the command, null on timers
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User|array|null $user): void
    {
        $this->user = is_array($user) ? User::fromArray($user) : $user;
    }

    /**
     * The channel where the command was called
     */
    public function getChannel(): ?Channel
    {
        return $this->channel;
    }

    public function setChannel(Channel|array|null $channel): void
    {
        $this->channel = is_array($channel) ? Channel::fromArray($channel) : $channel;
    }

    /**
     * The response url, null if it doesn't point to the Nightbot API
     */
    public function getResponseUrl(): ?string
    {
        return str_starts_with($this->responseUrl ?? '', 'https://api.nightbot.tv/') ? $this->responseUrl : null;
    }

    public function setResponseUrl(?string $responseUrl): void
    {
        $this->responseUrl = $responseUrl;
    }

    /**
     * Returns if the request has valid Nightbot headers
     */
    public function hasNightbotHeaders(): bool
    {
        return $this->channel !== null;
    }

    /**
     * Returns if the ip belongs to Nightbot, cache the result
     *
     * @param string $ip The ip that connected to your server.
     */
    public function isNightbotIp(string $ip): bool
    {
        $address = @inet_pton($ip);

        if ($address === false) {
            return false;
        }

        $host = $this->lookupHost($ip);

        if ($host === null || ! str_ends_with(strtolower($host), '.nightbot.net')) {
            return false;
        }

        foreach ($this->lookupIps($host, strlen($address) === 16) as $hostIp) {
            if (@inet_pton($hostIp) === $address) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns if the request is a timer request
     */
    public function isTimer(): bool
    {
        return $this->channel !== null && $this->user === null;
    }

    /**
     * Returns if the user is a moderator (owner counts as moderator)
     */
    public function isUserModerator(): bool
    {
        return in_array($this->user?->userLevel, ['owner', 'moderator'], true);
    }

    /**
     * Returns the provider of the Nightbot request
     *
     * If user is set, get the provider from the user. The provider value from the channel on Discord is the
     * provider that was used to link Nightbot to Discord. Timer messages have no user info in the headers, grab
     * the channel info instead. Timers don't work on Discord so the user should never be null there.
     */
    public function getProvider(): ?string
    {
        return $this->user?->provider ?? $this->channel?->provider;
    }

    protected function lookupHost(string $ip): ?string
    {
        return @gethostbyaddr($ip) ?: null;
    }

    /**
     * @return string[]
     */
    protected function lookupIps(string $host, bool $ipv6): array
    {
        $records = @dns_get_record($host, $ipv6 ? DNS_AAAA : DNS_A) ?: [];

        return array_column($records, $ipv6 ? 'ipv6' : 'ip');
    }

    private function parse(string $header): array
    {
        parse_str($header, $data);

        return $data;
    }
}
