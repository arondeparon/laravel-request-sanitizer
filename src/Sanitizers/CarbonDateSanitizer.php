<?php

namespace ArondeParon\RequestSanitizer\Sanitizers;

use ArondeParon\RequestSanitizer\Contracts\Sanitizer;
use Carbon\Carbon;

class CarbonDateSanitizer implements Sanitizer
{
    public function sanitize($input)
    {
        try {
            return Carbon::parse($input);
        } catch (\Exception $e) {
            return null;
        }
    }
}