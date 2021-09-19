<?php

declare(strict_types=1);

namespace NdaDayo\Yamato;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7;
use Mockery;
use NdaDayo\Yamato\Exception\ClientException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Psr\Http\Message\StreamInterface;

use function file_get_contents;

class YamatoTest extends TestCase
{
    private string $filePath;

    protected function setUp(): void
    {
        $this->filePath = './tests/DummyData/dummy.csv';
    }

    public function testSendShippingData(): void
    {
        $userId = 'user_id';
        $password = 'password';
        $endpoint = 'endpoint';
        $expectedRequestOptions = $this->expectedRequestOptions();
        $expectedEndPoint = $this->expectedEndPoint();

        $response = Mockery::mock(PsrResponseInterface::class);
        $response->shouldReceive('getBody')->andReturn($this->expected());

        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('request')
            ->andReturnUsing(function (string $method, string $endpoint, array $requestOptions) use ($expectedEndPoint, $expectedRequestOptions, $response) {
                $this->assertEquals('POST', $method);
                $this->assertEquals($expectedEndPoint, $endpoint);

                $this->assertSame($expectedRequestOptions['headers']['Content-Type'], $requestOptions['headers']['Content-Type']);
                $this->assertEquals($expectedRequestOptions['headers']['Content-Length'], $requestOptions['headers']['Content-Length']);
                $this->assertEquals($expectedRequestOptions['form_params']['userId'], $requestOptions['form_params']['userId']);
                $this->assertEquals($expectedRequestOptions['form_params']['password'], $requestOptions['form_params']['password']);
                $this->assertInstanceOf(StreamInterface::class, $expectedRequestOptions['form_params']['multipart'][0]['contents']);

                return $response;
            });

        $yamato = new Yamato($client, $userId, $password, $endpoint);
        $response = $yamato->sendShippingData($this->filePath);

        $this->assertEquals($this->expected(), $response->body());
    }

    public function testException(): void
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('you can set only csvFile.');

        $userId = 'user_id';
        $password = 'password';
        $endpoint = 'endpoint';
        $inValidFile = './tests/DummyData/dummy.html';

        $client = Mockery::mock(ClientInterface::class);
        $yamato = new Yamato($client, $userId, $password, $endpoint);
        $yamato->sendShippingData($inValidFile);
    }

    private function expected(): string
    {
        return file_get_contents('./tests/DummyData/dummy.html');
    }

    private function expectedEndPoint(): string
    {
        return 'endpoint';
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function expectedRequestOptions(): array
    {
        $file = Psr7\Utils::streamFor($this->filePath);

        return [
            'headers' => [
                'Content-Type' => 'multipart/form-data',
                'Content-Length' => $file->getSize(),
            ],
            'form_params' => [
                'uji.verbs' => 'fileUpload',
                'uji.id' => 'body',
                'uji.bean' => 'yamato.file.upload.bean.FileUploadBean',
                'uji.encoding' => 'Windows-31J',
                'userId' => 'user_id',
                'password' => 'password',
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
