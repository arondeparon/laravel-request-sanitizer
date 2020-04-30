<?php

namespace ArondeParon\RequestSanitizer\Tests\Sanitizers;

use ArondeParon\RequestSanitizer\Sanitizers\Capitalize;
use ArondeParon\RequestSanitizer\Sanitizers\FilterVars;
use ArondeParon\RequestSanitizer\Sanitizers\Lowercase;
use ArondeParon\RequestSanitizer\Sanitizers\RemoveNonNumeric;
use ArondeParon\RequestSanitizer\Sanitizers\Trim;
use ArondeParon\RequestSanitizer\Sanitizers\TrimDuplicateSpaces;
use ArondeParon\RequestSanitizer\Sanitizers\Uppercase;
use ArondeParon\RequestSanitizer\Tests\TestCase;

class SanizerTest extends TestCase
{
    public function test_uppercase_sanitizer()
    {
        $sanitizer = new Uppercase();
        $output = $sanitizer->sanitize('test');
        $this->assertEquals('TEST', $output);
    }

    public function test_lowercase_sanitizer()
    {
        $sanitizer = new Lowercase();
        $output = $sanitizer->sanitize('TEST');
        $this->assertEquals('test', $output);
    }

    public function test_capitalize_sanitizer()
    {
        $sanitizer = new Capitalize();
        $output = $sanitizer->sanitize('test');
        $this->assertEquals('Test', $output);
    }

    public function test_trim_sanitizer()
    {
        $sanitizer = new Trim();
        $output = $sanitizer->sanitize('test ');
        $this->assertEquals('test', $output);
    }

    public function test_trim_duplicate_spaces_sanitizer()
    {
        $sanitizer = new TrimDuplicateSpaces();
        $output = $sanitizer->sanitize('test     ');
        $this->assertEquals('test ', $output);
    }

    public function test_remove_non_numeric_sanitizer()
    {
        $sanitizer = new RemoveNonNumeric();
        $output = $sanitizer->sanitize('test1234-134AC~');
        $this->assertEquals('1234134', $output);
    }

    public function test_strip_tags_with_filter_vars()
    {
        $filter = FILTER_SANITIZE_STRING;
        $sanitizer = new FilterVars($filter);
        $output = $sanitizer->sanitize("<script>malicious code</script>");
        $this->assertEquals('malicious code', $output);
    }

    public function test_apply_default_filter_when_no_params_have_been_provided_in_filter_vars()
    {
        $sanitizer = new FilterVars();
        $output = $sanitizer->sanitize("no filter applied");
        $this->assertEquals('no filter applied', $output);
    }

    public function test_pass_filter_options_to_filter_vars()
    {
        $filter = FILTER_SANITIZE_NUMBER_FLOAT;
        $options = FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND;
        $sanitizer = new FilterVars($filter, $options);
        $output = $sanitizer->sanitize("442.34,34notallowed");
        $this->assertEquals("442.34,34", $output);
    }
}
