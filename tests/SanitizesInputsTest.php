<?php

namespace ArondeParon\RequestSanitizer\Tests;

use ArondeParon\RequestSanitizer\Contracts\Sanitizer;
use ArondeParon\RequestSanitizer\Sanitizers\Strip;
use ArondeParon\RequestSanitizer\Sanitizers\TrimDuplicateSpaces;
use ArondeParon\RequestSanitizer\Tests\Objects\Request;
use ArondeParon\RequestSanitizer\Tests\Objects\RequestWithRules;
use Illuminate\Support\Facades\Config;

class SanitizesInputsTest extends TestCase {

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

    public function test_it_will_handle_dot_notation()
    {
        $request = new Request([
            'foo' => [
                'bar' => 'This is a regular string',
            ],
        ]);

        $request->addSanitizers('foo.bar', [$sanitizer = \Mockery::mock(Sanitizer::class)]);

        /** @var \Mockery\MockInterface $sanitizer */
        $sanitizer->shouldReceive('sanitize')
            ->with('This is a regular string')
            ->once();

        $request->sanitize();
    }

    public function test_it_will_apply_the_default_sanitizers_to_existing_ones_()
    {
        $request = new Request([
            'foo' => '<script>This is an illegal script tag</script>',
        ]);

        $request->addSanitizers('foo', [$sanitizer = \Mockery::mock(Sanitizer::class)]);

        Config::set('sanitizer.defaults', [Strip::class]);

        $this->assertEquals(2, count($request->getSanitizers()['foo']));
    }

    public function test_it_will_apply_default_sanitizers_to_defined_rules()
    {
        $request = new RequestWithRules([
            'foo' => '<script>This is an illegal script tag</script>',
        ]);

        $request->addSanitizers('foo', [$sanitizer = \Mockery::mock(Sanitizer::class)]);

        Config::set('sanitizer.defaults', [Strip::class]);

        $this->assertEquals(3, count($request->getSanitizers()));
    }
}
