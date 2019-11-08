<?php

namespace ArondeParon\RequestSanitizer\Traits;

use Illuminate\Support\Arr;
use InvalidArgumentException;
use ArondeParon\RequestSanitizer\Contracts\Sanitizer;

trait SanitizesInputs
{
    /**
     * Get data to be validated from the request.
     *
     * @return array
     */
    public function validationData()
    {
        $this->sanitize();

        return $this->all();
    }

    /**
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function sanitize()
    {
        $input = $this->all();

        foreach ($this->getSanitizers() as $formKey => $sanitizers) {
            $sanitizers = (array) $sanitizers;
            foreach ($sanitizers as $key => $value) {
                if (is_string($key)) {
                    $sanitizer = app()->make($key, $value);
                } elseif (is_string($value)) {
                    $sanitizer = app()->make($value);
                } elseif ($value instanceof Sanitizer) {
                    $sanitizer = $value;
                } else {
                    throw new InvalidArgumentException('Could not resolve sanitizer from given properties');
                }
                Arr::set($input, $formKey, $sanitizer->sanitize($this->input($formKey, null)));
            }
        }

        return $this->replace($input);
    }

    /**
     * Add a single sanitizer.
     *
     * @param string $formKey
     * @param Sanitizer $sanitizer
     * @return $this
     */
    public function addSanitizer(string $formKey, $sanitizer)
    {
        if (!property_exists($this, 'sanitizers')) {
            $this->sanitizers = [];
        }

        if (!isset($this->sanitizers[$formKey])) {
            $this->sanitizers[$formKey] = [];
        }

        $this->sanitizers[$formKey][] = $sanitizer;

        return $this;
    }

    /**
     * Add multiple sanitizers.
     *
     * @param $formKey
     * @param array $sanitizers
     * @return $this
     */
    public function addSanitizers($formKey, $sanitizers = [])
    {
        foreach ($sanitizers as $sanitizer) {
            $this->addSanitizer($formKey, $sanitizer);
        }

        return $this;
    }

    /**
     * @param null $formKey
     * @return array
     */
    public function getSanitizers($formKey = null)
    {
        if (!property_exists($this, 'sanitizers')) {
            $this->sanitizers = [];
        }

        if ($formKey !== null) {
            return $this->sanitizers[$formKey] ?? [];
        }

        return $this->sanitizers;
    }
}
