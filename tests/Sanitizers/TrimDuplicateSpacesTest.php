<?php

namespace ArondeParon\RequestSanitizer\Tests\Sanitizers;

use ArondeParon\RequestSanitizer\Sanitizers\TrimDuplicateSpaces;
use ArondeParon\RequestSanitizer\Tests\TestCase;

class TrimDuplicateSpacesTest extends TestCase
{
    public function test_trim_duplicate_spaces_sanitizer()
    {
        $sanitizer = new TrimDuplicateSpaces();
        $output = $sanitizer->sanitize('test     ');
        $this->assertEquals('test ', $output);
    }
}
