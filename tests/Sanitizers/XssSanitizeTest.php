<?php

namespace ArondeParon\RequestSanitizer\Tests\Sanitizers;

use ArondeParon\RequestSanitizer\Sanitizers\XssSanitize;
use ArondeParon\RequestSanitizer\Tests\TestCase;

class TrimTest extends TestCase
{
    public function test_xss_sanitizer()
    {
        $sanitizer = new XssSanitize();
        $output = $sanitizer->sanitize("<?php echo 'test'; ?> <script> alert('test') </script>");
        $this->assertEquals("&lt;?php echo 'test'; ?&gt; &lt;script&gt; alert('test') &lt;/script&gt;", $output);
    }
