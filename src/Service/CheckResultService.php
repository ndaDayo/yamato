<?php

declare(strict_types=1);

namespace NdaDayo\Yamato\Service;

use function str_contains;

class CheckResultService
{
    private const SUCCESS_CODE = 'OK';

    public function isSuccess(string $responseBody): bool
    {
        return str_contains($responseBody, self::SUCCESS_CODE);
    }
}
