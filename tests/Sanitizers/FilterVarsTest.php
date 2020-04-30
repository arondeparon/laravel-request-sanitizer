<?php


namespace ArondeParon\RequestSanitizer\Tests\Sanitizers;

use ArondeParon\RequestSanitizer\Sanitizers\FilterVars;
use ArondeParon\RequestSanitizer\Tests\TestCase;

class FilterVarsTest extends TestCase
{
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