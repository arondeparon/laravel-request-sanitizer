<?php

namespace ArondeParon\RequestSanitizer\Traits;

use ArondeParon\RequestSanitizer\Contracts\Sanitizer;

trait SanitizesInputs
{
    /**
     * Get data to be validated from the request.
     *
     * @return array
     */
    protected function validationData()
    {
        $this->sanitize();

        return $this->all();
    }

    public function sanitize()
    {
        $input = $this->all();

        if (!property_exists($this, 'sanitizers')) {
            $this->sanitizers = [];
        }

        foreach ($this->sanitizers as $formKey => $sanitizers) {
            $sanitizers = (array) $sanitizers;
            foreach ($sanitizers as $sanitizer) {
                if (is_string($sanitizer)) {
                    $sanitizer = app()->make($sanitizer);
                }
                array_set($input, $formKey, $sanitizer->sanitize($this->input($formKey, null)));
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