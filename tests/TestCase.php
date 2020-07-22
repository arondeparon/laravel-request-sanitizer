<?php

namespace ArondeParon\RequestSanitizer\Tests;

use ArondeParon\RequestSanitizer\Tests\Objects\Request;
use Illuminate\Container\Container;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Validation\Factory as ValidationFactoryContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Validation\Factory as ValidationFactory;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Mockery as m;

abstract class TestCase extends OrchestraTestCase
{
    protected $mocks = [];

    protected function tearDown(): void
    {
        m::close();

        $this->mocks = [];

        parent::tearDown();
    }

    /**
     * Create a new request of the given type.
     *
     * @param array $payload
     * @param string $class
     * @return Request
     */
    protected function createRequest($payload = [], $class = Request::class)
    {
        $container = tap(new Container, function ($container) {
            $container->instance(
                ValidationFactoryContract::class,
                $this->createValidationFactory($container)
            );
        });

        $request = $class::create('/', 'GET', $payload);

        return $request->setRedirector($this->createMockRedirector($request))
            ->setContainer($container);
    }

    /**
     * Create a new validation factory.
     *
     * @param  \Illuminate\Container\Container  $container
     * @return \Illuminate\Validation\Factory
     */
    protected function createValidationFactory($container)
    {
        $translator = m::mock(Translator::class)->shouldReceive('get')
            ->zeroOrMoreTimes()->andReturn('error')->getMock();

        return new ValidationFactory($translator, $container);
    }

    /**
     * Create a mock redirector.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Routing\Redirector
     */
    protected function createMockRedirector($request)
    {
        $redirector = $this->mocks['redirector'] = m::mock(Redirector::class);

        $redirector->shouldReceive('getUrlGenerator')->zeroOrMoreTimes()
            ->andReturn($generator = $this->createMockUrlGenerator());

        $redirector->shouldReceive('to')->zeroOrMoreTimes()
            ->andReturn($this->createMockRedirectResponse());

        $generator->shouldReceive('previous')->zeroOrMoreTimes()
            ->andReturn('previous');

        return $redirector;
    }

    /**
     * Create a mock URL generator.
     *
     * @return \Illuminate\Routing\UrlGenerator
     */
    protected function createMockUrlGenerator()
    {
        return $this->mocks['generator'] = m::mock(UrlGenerator::class);
    }

    /**
     * Create a mock redirect response.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function createMockRedirectResponse()
    {
        return $this->mocks['redirect'] = m::mock(RedirectResponse::class);
    }
}