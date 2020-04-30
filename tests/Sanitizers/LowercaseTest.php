<?php

namespace ArondeParon\RequestSanitizer\Tests\Sanitizers;

use ArondeParon\RequestSanitizer\Sanitizers\Lowercase;
use ArondeParon\RequestSanitizer\Tests\TestCase;

class LowercaseTest extends TestCase
{
    public function test_lowercase_sanitizer()
    {
        $sanitizer = new Lowercase();
        $output = $sanitizer->sanitize('TEST');
        $this->assertEquals('test', $output);
    }
}
