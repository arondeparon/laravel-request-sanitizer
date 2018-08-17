<?php

namespace ArondeParon\RequestSanitizer\Sanitizers;

use ArondeParon\RequestSanitizer\Contracts\Sanitizer;

class TrimDuplicateSpaces implements Sanitizer
{
    /**
     * @param $input
     * @return string
     */
    public function sanitize($input)
    {
        return preg_replace('~\s{2,}~', ' ', $input);
    }
}