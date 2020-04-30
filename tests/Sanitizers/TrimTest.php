<?php

namespace ArondeParon\RequestSanitizer\Tests\Sanitizers;

use ArondeParon\RequestSanitizer\Sanitizers\Trim;
use ArondeParon\RequestSanitizer\Tests\TestCase;

class TrimTest extends TestCase
{
    public function test_trim_sanitizer()
    {
        $sanitizer = new Trim();
        $output = $sanitizer->sanitize('test ');
        $this->assertEquals('test', $output);
    }
}
