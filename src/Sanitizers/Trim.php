<?php

namespace ArondeParon\RequestSanitizer\Sanitizers;

use ArondeParon\RequestSanitizer\Contracts\Sanitizer;

class Trim implements Sanitizer
{
    /**
     * @param $input
     * @return string
     */
    public function sanitize($input)
    {
        return trim($input);
    }
}