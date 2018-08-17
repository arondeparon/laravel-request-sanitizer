<?php

namespace ArondeParon\RequestSanitizer\Tests\Objects;

use ArondeParon\RequestSanitizer\Traits\SanitizesInputs;
use Illuminate\Foundation\Http\FormRequest;

class Request extends FormRequest
{
    use SanitizesInputs;
}