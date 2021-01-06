<?php

namespace ArondeParon\RequestSanitizer\Sanitizers;

use ArondeParon\RequestSanitizer\Contracts\Sanitizer;

class Capitalize implements Sanitizer
{
    /**
     * @param $input
     * @return string
     */
    public function sanitize($input)
    {
        return ucfirst(strtolower($input));
    }
}
