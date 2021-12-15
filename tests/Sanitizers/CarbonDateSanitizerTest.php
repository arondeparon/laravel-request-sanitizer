<?php

namespace ArondeParon\RequestSanitizer\Tests\Sanitizers;

use ArondeParon\RequestSanitizer\Sanitizers\CarbonDateSanitizer;
use ArondeParon\RequestSanitizer\Tests\TestCase;
use Carbon\Carbon;

class CarbonDateSanitizerTest extends TestCase
{
    public function test_it_will_cast_a_valid_date_to_carbon()
    {
        $dateFormats = [
            '2021-12-15',
            '2021-12-15 12:00:00',
            '2021-12-15 12:00:00.000000',
        ];

        $sanitizer = new CarbonDateSanitizer();

        foreach ($dateFormats as $dateFormat) {
            $this->assertInstanceOf(Carbon::class, $sanitizer->sanitize($dateFormat));
        }
    }

    public function test_it_will_return_null_if_the_date_is_invalid()
    {
        $invalidDateFormats = [
            'derp',
            'May 33',
            '2021-13-30'
        ];

        $sanitizer = new CarbonDateSanitizer();

        foreach ($invalidDateFormats as $dateFormat) {
            $this->assertNull($sanitizer->sanitize($dateFormat), "It will not parse {$dateFormat}");
        }
    }
}