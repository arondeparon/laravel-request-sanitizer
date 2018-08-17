<?php

namespace ArondeParon\RequestSanitizer\Sanitizers;

use ArondeParon\RequestSanitizer\Contracts\Sanitizer;

class RemoveNonNumeric implements Sanitizer
{
    /**
     * @param $input
     * @return string
     */
    public function sanitize($input)
    {
        return preg_replace('~[^0-9]+~', '', $input);
    }
}