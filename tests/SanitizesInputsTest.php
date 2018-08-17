<?php

namespace ArondeParon\RequestSanitizer\Tests;

use ArondeParon\RequestSanitizer\Contracts\Sanitizer;
use ArondeParon\RequestSanitizer\Sanitizers\TrimDuplicateSpaces;
use ArondeParon\RequestSanitizer\Tests\Objects\Request;

class SanitizesInputsTest extends TestCase
{
    public function test_it_can_add_a_sanitizer()
    {
        $request = new Request();
        $request->addSanitizer('foo', new TrimDuplicateSpaces());
        $this->assertEquals(1, count($request->getSanitizers()));
    }

    public function test_it_can_retrieve_sanitizers_for_a_given_input()
    {
        $request = new Request();
        $request->addSanitizer('foo', new TrimDuplicateSpaces());
        $sanitizers = $request->getSanitizers('foo');

        $this->assertInstanceOf(TrimDuplicateSpaces::class, $sanitizers[0]);
    }

    public function test_it_will_return_an_empty_array_if_no_sanitizer_exists()
    {
        $request = new Request();
        $sanitizers = $request->getSanitizers('foo');

        $this->assertEmpty($sanitizers);
    }

    public function test_it_will_call_each_sanitizer_if_the_key_exists()
    {
        $sanitizers = [
            \Mockery::mock(Sanitizer::class),
            \Mockery::mock(Sanitizer::class),
            \Mockery::mock(Sanitizer::class),
        ];

        $request = new Request(['foo' => 'This is a regular string']);
        $request->addSanitizers('foo', $sanitizers);

        /** @var \Mockery\MockInterface $sanitizer */
        foreach ($sanitizers as $sanitizer) {
            $sanitizer->shouldReceive('sanitize')->once();
        }

        $request->sanitize();
    }
}