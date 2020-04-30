<?php

namespace ArondeParon\RequestSanitizer\Tests\Sanitizers;

use ArondeParon\RequestSanitizer\Sanitizers\RemoveNonNumeric;
use ArondeParon\RequestSanitizer\Tests\TestCase;

class RemoveNonNumericTest extends TestCase
{
    public function test_remove_non_numeric_sanitizer()
    {
        $sanitizer = new RemoveNonNumeric();
        $output = $sanitizer->sanitize('test1234-134AC~');
        $this->assertEquals('1234134', $output);
    }
}
