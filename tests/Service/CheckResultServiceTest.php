<?php

declare(strict_types=1);

namespace NdaDayo\Yamato\Service;

use PHPUnit\Framework\TestCase;

class CheckResultServiceTest extends TestCase
{
    public function testIsSuccessOK(): void
    {
        $responseBody = <<<EOM
<html>
<head>
  <meta charset="">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>dummy</title>
</head>

OK
</html>
EOM;

        $service = new CheckResultService();
        $actual = $service->isSuccess($responseBody);
        $this->assertTrue($actual);
    }

    public function testIsSuccessNG(): void
    {
        $responseBody = <<<EOM
<html>
<head>
  <meta charset="">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>dummy</title>
</head>

NG
</html>
EOM;

        $service = new CheckResultService();
        $actual = $service->isSuccess($responseBody);
        $this->assertFalse($actual);
    }

    public function testIsSuccessING(): void
    {
        $responseBody = <<<EOM
<html>
<head>
  <meta charset="">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>dummy</title>
</head>

ING
</html>
EOM;

        $service = new CheckResultService();
        $actual = $service->isSuccess($responseBody);
        $this->assertFalse($actual);
    }
}
