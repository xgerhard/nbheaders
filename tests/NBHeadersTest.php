<?php

namespace xgerhard\nbheaders\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use xgerhard\nbheaders\NBHeaders;

class NBHeadersTest extends TestCase
{
    private const CHANNEL = 'name=chan&displayName=Chan&provider=twitch&providerId=1';
    private const USER = 'name=bob&displayName=Bob%20B&provider=discord&providerId=2&userLevel=moderator';

    protected function tearDown(): void
    {
        unset($_SERVER['HTTP_NIGHTBOT_CHANNEL'], $_SERVER['HTTP_NIGHTBOT_USER'], $_SERVER['HTTP_NIGHTBOT_RESPONSE_URL']);
    }

    private function request(?string $channel, ?string $user = null, ?string $responseUrl = null): NBHeaders
    {
        $_SERVER['HTTP_NIGHTBOT_CHANNEL'] = $channel;
        $_SERVER['HTTP_NIGHTBOT_USER'] = $user;
        $_SERVER['HTTP_NIGHTBOT_RESPONSE_URL'] = $responseUrl;

        return new NBHeaders;
    }

    public function test_command(): void
    {
        $nb = $this->request(self::CHANNEL, self::USER, 'https://api.nightbot.tv/1/channel/send/abc');

        $this->assertTrue($nb->hasNightbotHeaders());
        $this->assertFalse($nb->isTimer());
        $this->assertTrue($nb->isUserModerator());
        $this->assertSame('1', $nb->getChannel()->providerId);
        $this->assertSame('Bob B', $nb->getUser()->displayName);
        $this->assertSame('discord', $nb->getProvider());
        $this->assertSame('https://api.nightbot.tv/1/channel/send/abc', $nb->getResponseUrl());
    }

    public function test_timer(): void
    {
        $nb = $this->request(self::CHANNEL);

        $this->assertTrue($nb->isTimer());
        $this->assertFalse($nb->isUserModerator());
        $this->assertSame('twitch', $nb->getProvider());
    }

    public function test_no_headers(): void
    {
        $nb = new NBHeaders;

        $this->assertFalse($nb->hasNightbotHeaders());
        $this->assertFalse($nb->isTimer());
        $this->assertNull($nb->getProvider());
    }

    public static function brokenHeaders(): array
    {
        return [
            'empty channel' => ['', null],
            'missing channel field' => ['name=x&displayName=x&provider=twitch', null],
            'array channel field' => [self::CHANNEL.'&name[]=x', null],
            'broken user is not a timer' => [self::CHANNEL, 'name=x'],
            'user without channel' => [null, self::USER],
        ];
    }

    #[DataProvider('brokenHeaders')]
    public function test_broken_headers_are_rejected(?string $channel, ?string $user): void
    {
        $nb = $this->request($channel, $user);

        $this->assertFalse($nb->hasNightbotHeaders());
        $this->assertFalse($nb->isTimer());
        $this->assertNull($nb->getUser());
    }

    public function test_setters(): void
    {
        $nb = new NBHeaders;
        $nb->setChannel(['name' => 'c', 'displayName' => 'C', 'provider' => 'youtube', 'providerId' => '3']);
        $nb->setUser(['name' => 'u', 'displayName' => 'U', 'provider' => 'youtube', 'providerId' => '4', 'userLevel' => 'owner']);

        $this->assertTrue($nb->hasNightbotHeaders());
        $this->assertTrue($nb->isUserModerator());

        $nb->setUser(null);
        $this->assertTrue($nb->isTimer());

        $this->expectException(InvalidArgumentException::class);
        $nb->setChannel(['name' => 'c']);
    }

    public static function badResponseUrls(): array
    {
        return array_map(fn ($url) => [$url], [
            '', 'nonsense', 'http://api.nightbot.tv/1', 'https://evil.com/', 'https://api.nightbot.tv.evil.com/',
            'https://evil.com/https://api.nightbot.tv', 'https://api.nightbot.tv@evil.com/', 'https://evil.com@api.nightbot.tv/',
            'https://api.nightbot.tv:8080/', 'https://evil.com\@api.nightbot.tv/', 'https://api.nightbot.tv', '//api.nightbot.tv/1',
        ]);
    }

    #[DataProvider('badResponseUrls')]
    public function test_response_url_must_be_nightbot(string $url): void
    {
        $nb = new NBHeaders;
        $nb->setResponseUrl($url);

        $this->assertNull($nb->getResponseUrl());
    }
}
