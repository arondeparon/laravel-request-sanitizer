<?php

namespace ArondeParon\RequestSanitizer\Sanitizers;

use ArondeParon\RequestSanitizer\Contracts\Sanitizer;

class FilterVars implements Sanitizer
{

    private $filter;
    private $options;


    /**
     * @param  int  $filter
     * @param  null  $options
     */
    function __construct(int $filter = FILTER_DEFAULT, $options = null)
    {
        $this->filter = $filter;
        $this->options = $options;
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
