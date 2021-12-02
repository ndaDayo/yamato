<?php

declare(strict_types=1);

namespace NdaDayo\Yamato;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7;
use GuzzleHttp\RequestOptions;
use Koriym\HttpConstants\Method;
use Koriym\HttpConstants\RequestHeader;
use NdaDayo\Yamato\Contracts\ResponseInterface;
use NdaDayo\Yamato\Contracts\YamatoInterface;
use NdaDayo\Yamato\Exception\ClientException;
use NdaDayo\Yamato\Exception\InvalidArgumentException;
use Throwable;

use function pathinfo;

use const PATHINFO_EXTENSION;

class Yamato implements YamatoInterface
{
    public function __construct(
        private ClientInterface $client,
        private string $userId,
        private string $password,
        private string $endpoint
    ) {
    }

    public function sendShippingData(string $filePath): ResponseInterface
    {
        try {
            if (! $this->isCsvFile($filePath)) {
                throw new InvalidArgumentException('you can set only csvFile. ');
            }

            $response = $this->client->request(
                Method::POST,
                $this->endpoint,
                $this->requestOptions($filePath)
            );

            return new Response($response);
        } catch (Throwable $e) {
            throw new ClientException($e->getMessage(), (int) $e->getCode());
        }
    }

    private function isCsvFile(string $filePath): bool
    {
        return pathinfo($filePath, PATHINFO_EXTENSION) === 'csv';
    }

    /**
     * @return array<string, mixed>
     */
    private function requestOptions(string $filePath): array
    {
        $file = Psr7\Utils::streamFor($filePath);

        return [
            RequestOptions::HEADERS => [
                RequestHeader::CONTENT_LENGTH => $file->getSize(),
            ],
            RequestOptions::MULTIPART => [
                [
                    'name' => 'uji.verbs',
                    'contents' => 'fileUpload',
                ],
                [
                    'name' => 'uji.id',
                    'contents' => 'body',
                ],
                [
                    'name' => 'uji.verbs',
                    'contents' => 'fileUpload',
                ],
                [
                    'name' => 'uji.bean',
                    'contents' => 'yamato.file.upload.bean.FileUploadBean',
                ],
                [
                    'name' => 'uji.encoding',
                    'contents' => 'Windows-31J',
                ],
                [
                    'name' => 'userId',
                    'contents' => $this->userId,
                ],
                [
                    'name' => 'password',
                    'contents' => $this->password,
                ],
                [
                    'name' => 'file',
                    'contents' => Psr7\Utils::tryFopen($filePath, 'r'),
                ],
            ],
        ];
    }
}
