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

    /**
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function sanitize()
    {
        $input = $this->all();

        foreach ($this->getSanitizers() as $formKey => $sanitizers) {
            // Handle wildcards in the form key
            if (str_contains($formKey, '*')) {
                $pattern = str_replace('*', '[^.]+', $formKey);
                $matchingKeys = $this->findMatchingKeys($input, $pattern);
                
                foreach ($matchingKeys as $matchingKey) {
                    if (!data_get($input, $matchingKey)) {
                        continue;
                    }
                    $this->applySanitizers($input, $matchingKey, $sanitizers);
                }
                continue;
            }

            if (!data_get($input, $formKey)) {
                // If the request does not have a property for this key, there is no need to sanitize anything.
                continue;
            }
            
            $this->applySanitizers($input, $formKey, $sanitizers);
        }

        return $this->replace($input);
    }

    /**
     * Apply sanitizers to a specific form key
     * 
     * @param array $input
     * @param string $formKey
     * @param array $sanitizers
     * @return void
     */
    protected function applySanitizers(&$input, $formKey, $sanitizers)
    {
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
            $this->replace($input);
        }
    }

    /**
     * Find all keys in the input array that match the given pattern
     * 
     * @param array $input
     * @param string $pattern
     * @return array
     */
    protected function findMatchingKeys($input, $pattern)
    {
        $matches = [];
        $pattern = '/^' . $pattern . '$/';

        $this->findMatchingKeysRecursive($input, '', $pattern, $matches);

        return $matches;
    }

    /**
     * Recursively find all keys in the input array that match the given pattern
     * 
     * @param array $input
     * @param string $prefix
     * @param string $pattern
     * @param array &$matches
     * @return void
     */
    protected function findMatchingKeysRecursive($input, $prefix, $pattern, &$matches)
    {
        foreach ($input as $key => $value) {
            $currentPath = $prefix ? $prefix . '.' . $key : $key;
            
            if (preg_match($pattern, $currentPath)) {
                $matches[] = $currentPath;
            }
            
            if (is_array($value)) {
                $this->findMatchingKeysRecursive($value, $currentPath, $pattern, $matches);
            }
        }
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
