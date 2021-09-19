<?php

declare(strict_types=1);

namespace NdaDayo\Yamato\Contracts;

interface YamatoInterface
{
    public function sendShippingData(string $filePath): ResponseInterface;
}
