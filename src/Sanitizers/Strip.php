<?php


namespace ArondeParon\RequestSanitizer\Sanitizers;


use ArondeParon\RequestSanitizer\Contracts\Sanitizer;

class Strip implements Sanitizer {


    /**
     * @param $input
     * @return mixed
     */
    public function sanitize($input)
    {
        return filter_var($input, FILTER_SANITIZE_STRIPPED);
    }
}
