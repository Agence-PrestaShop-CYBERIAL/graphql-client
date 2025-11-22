<?php

namespace AgenceCyberial\GraphqlClient\Classes;

use Closure;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Assert as PHPUnit;

class Factory
{
    /**
     * The stubbed responses.
     *
     * @var array
     */
    protected $stubCallbacks = [];

    /**
     * The recorded requests.
     *
     * @var array
     */
    protected $recorded = [];

    /**
     * Create a new factory instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Register a stub callable that will intercept requests and be able to return stub responses.
     *
     * @param  array|callable  $callback
     * @return $this
     */
    public function fake($callback = null)
    {
        if (is_null($callback)) {
            $callback = function () {
                return [];
            };
        }

        if (is_array($callback)) {
            foreach ($callback as $url => $callable) {
                $this->stubCallbacks[$url] = is_callable($callable) ? $callable : function () use ($callable) {
                    return $callable;
                };
            }
        } else {
            $this->stubCallbacks['*'] = is_callable($callback) ? $callback : function () use ($callback) {
                return $callback;
            };
        }

        return $this;
    }

    /**
     * Record a request.
     *
     * @param  \AgenceCyberial\GraphqlClient\Classes\Client  $request
     * @return void
     */
    public function recordRequest($request)
    {
        $this->recorded[] = $request;
    }

    /**
     * Assert that a request / query was sent.
     *
     * @param  callable|int  $callback
     * @return void
     */
    public function assertSent($callback)
    {
        if (is_int($callback)) {
            return $this->assertSentCount($callback);
        }

        PHPUnit::assertTrue(
            $this->recorded($callback)->count() > 0,
            'An expected request was not recorded.'
        );
    }

    /**
     * Assert that a request / query was not sent.
     *
     * @param  callable  $callback
     * @return void
     */
    public function assertNotSent($callback)
    {
        PHPUnit::assertFalse(
            $this->recorded($callback)->count() > 0,
            'Unexpected request was recorded.'
        );
    }

    /**
     * Assert that nothing was sent.
     *
     * @return void
     */
    public function assertNothingSent()
    {
        PHPUnit::assertEmpty($this->recorded, 'Requests were recorded.');
    }

    /**
     * Assert the count of requests.
     *
     * @param  int  $count
     * @return void
     */
    public function assertSentCount($count)
    {
        PHPUnit::assertCount($count, $this->recorded);
    }

    /**
     * Get a collection of the recorded requests.
     *
     * @param  callable  $callback
     * @return \Illuminate\Support\Collection
     */
    public function recorded($callback)
    {
        if (empty($this->recorded)) {
            return collect();
        }

        $callback = $callback ?: function () {
            return true;
        };

        return collect($this->recorded)->filter(function ($request) use ($callback) {
            return $callback($request);
        });
    }

    /**
     * Execute a method against a new pending request instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (! empty($this->stubCallbacks)) {
            return $this->stub($this->stubCallbacks)->$method(...$parameters);
        }

        return (new Client(config('graphqlclient.graphql_endpoint')))->$method(...$parameters);
    }

    /**
     * Create a new fake client instance.
     *
     * @param  array  $callbacks
     * @return \AgenceCyberial\GraphqlClient\Classes\FakeClient
     */
    protected function stub($callbacks)
    {
        return new FakeClient(config('graphqlclient.graphql_endpoint'), $this, $callbacks);
    }
}
