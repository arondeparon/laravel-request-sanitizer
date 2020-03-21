<?php

namespace ArondeParon\RequestSanitizer\Tests\Objects;

use ArondeParon\RequestSanitizer\Traits\SanitizesInputs;
use Illuminate\Foundation\Http\FormRequest;

class RequestWithRules extends FormRequest
{
    use SanitizesInputs;

    public function rules()
    {
        return [
            'email' => 'required|email',
            'password' => 'string',
        ];
    }

}
