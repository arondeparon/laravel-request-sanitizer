<?php

namespace ArondeParon\RequestSanitizer\Traits;

use ArondeParon\RequestSanitizer\Contracts\Sanitizer;
use Illuminate\Support\Arr;
use InvalidArgumentException;

trait SanitizesInputs
{
    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->sanitize();
    }

    public function hasWildcardSanitizer()
    {
        return array_key_exists('*', $this->getSanitizers());
    }

    protected function doSanitize(&$input, $key, $value, $formKey)
    {
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
        $this->replace($input);
    }

    /**
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function sanitize()
    {
        $input = $this->all();

        // Run Wildcards First
        if ($this->hasWildcardSanitizer()) {
            $sanitizers = (array) $this->getSanitizers()['*'];
            foreach ($input as $formKey => $inputValue) {
                if (str_contains($formKey, 'url')) {
                    // Urls are case sensitive
                    // Urls should be process independently
                    continue;
                }

                foreach ($sanitizers as $key => $value) {
                    $this->doSanitize($input, $key, $value, $formKey);
                }
            }
        }

        foreach ($this->getSanitizers() as $formKey => $sanitizers) {
            if (!$this->has($formKey)) {
                continue;
            }
            $sanitizers = (array) $sanitizers;
            foreach ($sanitizers as $key => $value) {
                $this->doSanitize($input, $key, $value, $formKey);
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
