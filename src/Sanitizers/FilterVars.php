<?php

namespace ArondeParon\RequestSanitizer\Sanitizers;

use ArondeParon\RequestSanitizer\Contracts\Sanitizer;

class FilterVars implements Sanitizer
{

    private $filter;
    private $options;

    /**
     * FilterVars constructor.
     * @param  array  $opts
     */
    function __construct(array $opts)
    {
        $this->filter = $opts['filter'] ?? null;
        $this->options = $opts['options'] ?? null;
    }

    /**
     * @param $input
     * @return string
     */
    public function sanitize($input)
    {
        return filter_var($input, $this->filter, $this->options);
    }

}
