<?php

namespace ArondeParon\RequestSanitizer\Tests;

use ArondeParon\RequestSanitizer\Contracts\Sanitizer;
use ArondeParon\RequestSanitizer\Sanitizers\Capitalize;
use ArondeParon\RequestSanitizer\Sanitizers\Lowercase;
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

    public function test_it_will_chain_the_result_of_each_sanitizer()
    {
        $sanitizers = [
            Lowercase::class,
            Capitalize::class,
        ];

        $request = $this->createRequest([
            'foo' => 'this is a lower case string with a CAPITAL word'
        ]);
        $request->addSanitizers('foo', $sanitizers);

        $request->validateResolved();

        $this->assertEquals('This is a lower case string with a capital word', $request->input('foo'));
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

    public function test_it_will_handle_wildcards_in_arrays()
    {
        $request = $this->createRequest([
            'users' => [
                ['name' => 'JOHN DOE'],
                ['name' => 'JANE DOE'],
            ]
        ]);

        $request->addSanitizers('users.*.name', [new Lowercase()]);
        $request->validateResolved();

        $this->assertEquals('john doe', $request->input('users.0.name'));
        $this->assertEquals('jane doe', $request->input('users.1.name'));
    }

    public function test_it_will_handle_multiple_wildcards()
    {
        $request = $this->createRequest([
            'departments' => [
                'sales' => [
                    'employees' => [
                        ['name' => 'JOHN DOE'],
                        ['name' => 'JANE DOE'],
                    ]
                ],
                'marketing' => [
                    'employees' => [
                        ['name' => 'BOB SMITH'],
                        ['name' => 'ALICE JONES'],
                    ]
                ]
            ]
        ]);

        $request->addSanitizers('departments.*.employees.*.name', [new Lowercase()]);
        $request->validateResolved();

        $this->assertEquals('john doe', $request->input('departments.sales.employees.0.name'));
        $this->assertEquals('jane doe', $request->input('departments.sales.employees.1.name'));
        $this->assertEquals('bob smith', $request->input('departments.marketing.employees.0.name'));
        $this->assertEquals('alice jones', $request->input('departments.marketing.employees.1.name'));
    }

    public function test_it_will_not_match_invalid_wildcard_patterns()
    {
        $request = $this->createRequest([
            'users' => [
                ['name' => 'JOHN DOE'],
                ['name' => 'JANE DOE'],
            ]
        ]);

        $request->addSanitizers('invalid.*.pattern', [$sanitizer = \Mockery::mock(Sanitizer::class)]);
        
        $sanitizer->shouldNotReceive('sanitize');
        
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

    public function test_it_will_not_sanitize_properties_that_are_not_present()
    {
        $request = new Request();

        $request->addSanitizers('foo', [$sanitizer = \Mockery::mock(Sanitizer::class)]);

        $sanitizer->shouldNotReceive('sanitize');

        $request->sanitize();

        $this->assertNull($request->input('foo'));
    }
}