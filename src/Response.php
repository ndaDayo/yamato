<?php

declare(strict_types=1);

namespace NdaDayo\Yamato;

use NdaDayo\Yamato\Contracts\ResponseInterface;
use NdaDayo\Yamato\Service\CheckResultService;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

class Response implements ResponseInterface
{
    /** @var PsrResponseInterface $response */
    private $response;

    public function __construct(PsrResponseInterface $response)
    {
        $this->response = $response;
    }

    public function body(): string
    {
        return (string) $this->response->getBody();
    }

    public function isSuccess(): bool
    {
        $service = new CheckResultService();

        return $service->isSuccess($this->body());
    }
}
