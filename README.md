# NdaDayo.Yamato

[![Packagist](https://img.shields.io/badge/packagist-v1.0.2-blue.svg)](https://packagist.org/packages/ndaDayo/nextengine)
[![CI](https://github.com/ndaDayo/yamato/actions/workflows/ci.yml/badge.svg)](https://github.com/ndaDayo/yamato/actions/workflows/ci.yml)

ヤマト運輸宅急便追跡サービスに出荷予定データを送信するためのAPIです。

## Installation

```
$ composer require ndadayo/yamato
```

## Usage

```
<?php 
use GuzzleHttp\Client;
use NdaDayo\Yamato\Yamato;

$client = new Client();

$userId = 'ヤマト運輸より発行されたユーザーID';
$password = 'ヤマト運輸より発行されたパスワード';
$endpoint = 'ヤマト運輸より発行されたエンドポイント';
$yamato = new Yamato($client, $userId, $password, $endpoint);

$filePath = '出荷予定データのファイルパス'
$yamato->sendShippingData($filePath);
```

## Test

```
$ composer tests
```