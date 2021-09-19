<?php

declare(strict_types=1);

namespace NdaDayo\Yamato;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7;
use GuzzleHttp\RequestOptions;
use Koriym\HttpConstants\MediaType;
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
     * @return array<string, array<string, mixed>>
     */
    private function requestOptions(string $filePath): array
    {
        $file = Psr7\Utils::streamFor($filePath);

        return [
            RequestOptions::HEADERS => [
                RequestHeader::CONTENT_TYPE => MediaType::MULTIPART_FORM_DATA,
                RequestHeader::CONTENT_LENGTH => $file->getSize(),
            ],
            RequestOptions::FORM_PARAMS => [
                'uji.verbs' => 'fileUpload',
                'uji.id' => 'body',
                'uji.bean' => 'yamato.file.upload.bean.FileUploadBean',
                'uji.encoding' => 'Windows-31J',
                'userId' => $this->userId,
                'password' => $this->password,
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => $file,
                    ],
                ],
            ],
        ];
    }
}
