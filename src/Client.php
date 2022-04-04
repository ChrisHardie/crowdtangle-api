<?php

namespace ChrisHardie\CrowdtangleApi;

use ChrisHardie\CrowdtangleApi\Exceptions\BadRequest;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;

class Client
{
    /**
     * @var TokenProvider
     */
    private TokenProvider $tokenProvider;

    /**
     * @var GuzzleClient
     */
    protected GuzzleClient $client;

    /**
     * @param string|null $accessToken
     * @param GuzzleClient|null $client
     */
    public function __construct(string $accessToken = null, ClientInterface $client = null)
    {
        if ($accessToken instanceof TokenProvider) {
            $this->tokenProvider = $accessToken;
        }
        if (is_string($accessToken)) {
            $this->tokenProvider = new InMemoryTokenProvider($accessToken);
        }

        $this->client = $client ?? new GuzzleClient();
    }

    /**
     * Retrieve the lists, saved searches and saved post lists
     *
     * @link https://github.com/CrowdTangle/API/wiki/lists
     *
     * @return array
     * @throws BadRequest
     * @throws GuzzleException
     * @throws JsonException
     */
    public function getLists(): array
    {
        $body = $this->endpointRequest('lists');
        return $body['result']['lists'];
    }

    /**
     * Retrieve the accounts for a given list.
     * Accounts may only be retrieved for lists of type LIST, as saved searches and saved posts
     * do not have associated accounts.
     *
     * @link https://github.com/CrowdTangle/API/wiki/List-Accounts
     *
     * @param int $listId
     * @return array
     * @throws BadRequest
     * @throws GuzzleException
     * @throws JsonException
     */
    public function getAccountsForList(int $listId): array
    {
        $body = $this->endpointRequest('lists/' . $listId . '/accounts');
        return $body['result']['accounts'];
    }

    /**
     * Retrieve a set of posts for the given parameters.
     *
     * @link https://github.com/CrowdTangle/API/wiki/Posts
     *
     * @param array|null $parameters
     * @return array
     * @throws BadRequest
     * @throws GuzzleException
     * @throws JsonException
     */
    public function getPosts(?array $parameters = []): array
    {
        $body = $this->endpointRequest('posts', $parameters);
        return $body['result']['posts'];
    }

    /**
     * Retrieves a specific post.
     * The ID format for Facebook and Instagram are different.
     * For Instagram, it's [post_id]_[page_id]
     * For Facebook, it's [page_id]_[post_id]
     *
     * @link https://github.com/CrowdTangle/API/wiki/Posts#get-postid
     *
     * @param string $postId
     * @return array
     * @throws BadRequest
     * @throws GuzzleException
     * @throws JsonException
     */
    public function getPost(string $postId): array
    {
        $body = $this->endpointRequest('post/' . $postId);
        if (! empty($body['result']['posts'][0])) {
            return $body['result']['posts'][0];
        }
        return [];
    }

    /**
     * @param string     $endpoint
     * @param array|null $arguments
     * @return array
     *
     * @throws BadRequest
     * @throws GuzzleException
     * @throws JsonException
     */
    public function endpointRequest(string $endpoint, ? array $arguments = []): array
    {
        try {
            $response = $this->client->get($this->getEndpointUrl($endpoint), [
                'headers' => $this->getHeaders(),
                'query' => $arguments,
            ]);
        } catch (ClientException $exception) {
            $response = $this->endpointRequest($endpoint, $arguments);
        }

        return json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR) ?? [];
    }

    protected function getEndpointUrl(string $endpoint): string
    {
        return "https://api.crowdtangle.com/{$endpoint}";
    }

    /**
     * Get the access token.
     */
    public function getAccessToken(): string
    {
        return $this->tokenProvider->getToken();
    }

    /**
     * Set the access token.
     */
    public function setAccessToken(string $accessToken): self
    {
        $this->tokenProvider = new InMemoryTokenProvider($accessToken);

        return $this;
    }

    /**
     * Get the HTTP headers.
     */
    protected function getHeaders(array $headers = []): array
    {
        $auth = [];
        if ($this->tokenProvider) {
            $auth = $this->getHeadersForApiToken($this->tokenProvider->getToken());
        }

        return array_merge($auth, $headers);
    }

    /**
     * @param $token
     * @return array
     */
    protected function getHeadersForApiToken($token): array
    {
        return [
            'x-api-token' => $token,
        ];
    }

    /**
     * @throws JsonException
     */
    protected function determineException(ClientException $exception): BadRequest|ClientException
    {
        if (in_array($exception->getResponse()->getStatusCode(), [400, 409])) {
            return new BadRequest($exception->getResponse());
        }

        return $exception;
    }
}
