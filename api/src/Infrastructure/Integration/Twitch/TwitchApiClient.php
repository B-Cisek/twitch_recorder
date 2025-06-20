<?php

declare(strict_types=1);

namespace App\Infrastructure\Integration\Twitch;

use App\Infrastructure\Exception\UserNotLiveException;
use App\Infrastructure\Exception\UserNotFoundException;
use App\Infrastructure\Integration\Twitch\DTO\StreamInfo;
use App\Infrastructure\Integration\Twitch\DTO\UserInfo;
use App\Infrastructure\Integration\Twitch\Enum\StreamType;
use App\Infrastructure\Integration\Twitch\Exception\TwitchApiException;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TwitchApiClient implements TwitchApiClientInterface
{
    private const string BASE_URL = 'https://api.twitch.tv/helix';
    private const string TOKEN_URL = 'https://id.twitch.tv/oauth2/token';
    private const string TOKEN_KEY = 'TWITCH_TOKEN';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
        #[Autowire(service: 'cache.long')]
        private readonly CacheInterface $cache,
        private readonly string $clientId,
        private readonly string $clientSecret,
        private ?string $accessToken = null
    )
    {
        $this->accessToken = $this->cache->get(self::TOKEN_KEY, function (ItemInterface $item) {
            $data = $this->getAccessToken();
            $item->expiresAfter($data['expires_in'] - 10);
            $this->logger->info('Access token has been saved in cache');
            return $data['access_token'];
        });
    }

    private function makeRequest(string $method, string $endpoint, array $params = []): array
    {
        try {
            $headers = [
                'Client-ID' => $this->clientId,
                'Authorization' => 'Bearer ' . $this->accessToken
            ];

            $url = self::BASE_URL . $endpoint;
            if (!empty($params) && $method === 'GET') {
                $url .= '?' . http_build_query($params);
            }

            $response = $this->httpClient->request($method, $url, [
                'headers' => $headers,
                'json' => $method !== 'GET' ? $params : null,
            ]);

            if ($response->getStatusCode() >= 400) {
                throw new TwitchApiException(
                    'Twitch API request failed with status: ' . $response->getStatusCode()
                );
            }

            return $response->toArray();
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Twitch API request failed with error', [
                'exception_message' => $e->getMessage(),
            ]);
            throw new TwitchApiException('Twitch API transport error: ' . $e->getMessage(), 0, $e);
        } catch (\Exception $e) {
            $this->logger->error('Twitch API request failed with error', [
                'exception_message' => $e->getMessage(),
            ]);
            throw new TwitchApiException('Twitch API error: ' . $e->getMessage(), 0, $e);
        }
    }


    /** @return array{access_token: string, expires_in: int} */
    private function getAccessToken(): array
    {
        try {
            $response = $this->httpClient->request('POST', self::TOKEN_URL, [
                'body' => [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'grant_type' => 'client_credentials'
                ]
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new TwitchApiException('Twitch API request failed with status: ' . $response->getStatusCode());
            }

            $data = $response->toArray();

            return [
                'access_token' => $data['access_token'],
                'expires_in' => $data['expires_in']
            ];
        } catch (\Exception $e) {
            $this->logger->error('Twitch API request failed with error', [
                'exception_message' => $e->getMessage(),
            ]);
            throw new TwitchApiException('Failed to get access token: ' . $e->getMessage(), 0, $e);
        }
    }

    public function getUserInfo(string $username): UserInfo
    {
        $response = $this->makeRequest('GET', '/users', ['login' => $username]);

        if (empty($response['data'])) {
            throw new UserNotFoundException($username);
        }

        return new UserInfo(
            id: $response['data'][0]['id'],
            login: $response['data'][0]['login'],
            displayName: $response['data'][0]['display_name'],
            type: $response['data'][0]['type'],
            broadcasterType: $response['data'][0]['broadcaster_type'],
            description: $response['data'][0]['description'],
            profileImageUrl: $response['data'][0]['profile_image_url'],
            offlineImageUrl: $response['data'][0]['offline_image_url'],
            viewCount: $response['data'][0]['view_count'],
            createdAt: new DateTimeImmutable($response['data'][0]['created_at']),
        );
    }

    public function getStreamInfo(string $username): StreamInfo
    {
        $response = $this->makeRequest('GET', '/streams', ['user_login' => $username]);

        if (empty($response['data'])) {
            throw new UserNotLiveException($username);
        }

        return new StreamInfo(
            id: $response['data'][0]['id'],
            userId: $response['data'][0]['user_id'],
            userLogin: $response['data'][0]['user_login'],
            userName: $response['data'][0]['user_name'],
            gameId: $response['data'][0]['game_id'],
            gameName: $response['data'][0]['game_name'],
            type: StreamType::from($response['data'][0]['type']),
            title: $response['data'][0]['title'],
            viewerCount: $response['data'][0]['viewer_count'],
            startedAt: new DateTimeImmutable($response['data'][0]['started_at']),
            language: $response['data'][0]['language'],
            thumbnailUrl: $response['data'][0]['thumbnail_url'],
            isMature: $response['data'][0]['is_mature'],
        );
    }
}