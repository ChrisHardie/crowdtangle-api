<?php

namespace ChrisHardie\CrowdtangleApi\Exceptions;

use Exception;
use JsonException;
use Psr\Http\Message\ResponseInterface;

class BadRequest extends Exception
{
    /**
     * @var ResponseInterface
     */
    public ResponseInterface $response;

    /**
     * The CrowdTangle error code supplied in the response.
     *
     * @var int|null
     */
    public ?int $crowdtangleCode;

    /**
     * @throws JsonException
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;

        $body = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        if ($body !== null) {
            if (isset($body['code'])) {
                $this->crowdtangleCode = $body['code'];
            }

            parent::__construct($body['message']);
        }
    }
}
