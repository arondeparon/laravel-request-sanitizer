<?php

namespace ArondeParon\RequestSanitizer\Tests;

use ArondeParon\RequestSanitizer\Contracts\Sanitizer;
use ArondeParon\RequestSanitizer\Sanitizers\TrimDuplicateSpaces;
use ArondeParon\RequestSanitizer\Tests\Objects\Request;
use Illuminate\Validation\ValidationException;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class SanitizesInputsTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test_it_can_add_a_sanitizer()
    {
        $request = $this->createRequest();
        $request->addSanitizer('foo', new TrimDuplicateSpaces());

        self::assertCount(1, $request->getSanitizers());
    }

    public function test_it_can_retrieve_sanitizers_for_a_given_input()
    {
        $request = $this->createRequest();
        $request->addSanitizer('foo', new TrimDuplicateSpaces());
        $sanitizers = $request->getSanitizers('foo');

        self::assertInstanceOf(TrimDuplicateSpaces::class, $sanitizers[0]);
    }

    public function test_it_will_return_an_empty_array_if_no_sanitizer_exists()
    {
        $request = $this->createRequest();
        $sanitizers = $request->getSanitizers('foo');

        self::assertEmpty($sanitizers);
    }

    public function test_it_will_call_each_sanitizer_if_the_key_exists()
    {
        $sanitizers = [
            \Mockery::mock(Sanitizer::class),
            \Mockery::mock(Sanitizer::class),
            \Mockery::mock(Sanitizer::class),
        ];

        $request = $this->createRequest(['foo' => 'This is a regular string']);
        $request->addSanitizers('foo', $sanitizers);

        /** @var \Mockery\MockInterface $sanitizer */
        foreach ($sanitizers as $sanitizer) {
            $sanitizer->shouldReceive('sanitize')->once();
        }

        $request->validateResolved();
    }

    public function test_it_will_handle_dot_notation()
    {
        $request = $this->createRequest([
            'foo' => [
                'bar' => 'This is a regular string',
            ],
        ]);

        $request->addSanitizers('foo.bar', [$sanitizer = \Mockery::mock(Sanitizer::class)]);

        /** @var \Mockery\MockInterface $sanitizer */
        $sanitizer->shouldReceive('sanitize')
            ->with('This is a regular string')
            ->once();

        $request->validateResolved();
    }

    public function test_it_should_sanitize_even_if_the_request_is_invalid()
    {
        $request = $this->createRequest([
            'bar' => 'This is a regular string',
        ], RequiredFieldsRequest::class);

        $request->addSanitizers('bar', [$sanitizer = \Mockery::mock(Sanitizer::class)]);

        /** @var \Mockery\MockInterface $sanitizer */
        $sanitizer->shouldReceive('sanitize')
            ->with('This is a regular string')
            ->once();

        $this->expectException(ValidationException::class);

        $request->validateResolved();
    }
}

class RequiredFieldsRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required'
        ];
    }
}