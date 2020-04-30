<?php

namespace ArondeParon\RequestSanitizer\Tests\Sanitizers;

use ArondeParon\RequestSanitizer\Sanitizers\Capitalize;
use ArondeParon\RequestSanitizer\Tests\TestCase;

class CapitalizeTest extends TestCase
{
    public function test_capitalize_sanitizer()
    {
        $sanitizer = new Capitalize();
        $output = $sanitizer->sanitize('test');
        $this->assertEquals('Test', $output);
    }
}
