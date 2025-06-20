<?php

declare(strict_types=1);

namespace App\Infrastructure\Integration\Twitch;

use App\Infrastructure\Integration\Twitch\Exception\TwitchApiException;
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
}