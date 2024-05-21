<?php

declare(strict_types=1);

namespace App\Tests\Functional\Data;

use stdClass;

class DailyData
{
    public static function today(): stdClass
    {
        $result = new stdClass();
        $result->GetCursOnDateResult = new stdClass();
        $result->GetCursOnDateResult->any = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'DailyAny.xml');
        $result->GetCursOnDateResult->schema = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'DailySchema.xml');
        return $result;
    }

    public static function previous(): stdClass
    {
        $result = new stdClass();
        $result->GetCursOnDateResult = new stdClass();
        $result->GetCursOnDateResult->any = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'DailyPreviousAny.xml');
        $result->GetCursOnDateResult->schema = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'DailyPreviousSchema.xml');
        return $result;
    }
}
