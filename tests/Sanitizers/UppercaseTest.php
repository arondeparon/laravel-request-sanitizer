<?php

namespace ArondeParon\RequestSanitizer\Tests\Sanitizers;

use ArondeParon\RequestSanitizer\Sanitizers\Uppercase;
use ArondeParon\RequestSanitizer\Tests\TestCase;

class UppercaseTest extends TestCase
{
    public function test_uppercase_sanitizer()
    {
        $sanitizer = new Uppercase();
        $output = $sanitizer->sanitize('test');
        $this->assertEquals('TEST', $output);
    }
}
