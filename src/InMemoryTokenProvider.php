<?php

namespace ChrisHardie\CrowdtangleApi;

class InMemoryTokenProvider implements TokenProvider
{
    /**
     * @var string
     */
    private string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
