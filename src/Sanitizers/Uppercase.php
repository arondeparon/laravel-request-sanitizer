<?php

namespace ArondeParon\RequestSanitizer\Sanitizers;

use ArondeParon\RequestSanitizer\Contracts\Sanitizer;

class Uppercase implements Sanitizer
{
    /**
     * @param $input
     * @return string
     */
    public function sanitize($input)
    {
        return mb_strtoupper($input, 'UTF-8');
    }
}
