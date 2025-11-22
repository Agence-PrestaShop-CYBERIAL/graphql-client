<?php

namespace AgenceCyberial\GraphqlClient\Classes;

use AgenceCyberial\GraphqlClient\Enums\Format;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class FakeClient extends Client
{
    /**
     * The factory instance.
     *
     * @var \AgenceCyberial\GraphqlClient\Classes\Factory
     */
    protected $factory;

    /**
     * The stub callbacks.
     *
     * @var array
     */
    protected $callbacks;

    /**
     * Create a new fake client instance.
     *
     * @param  string|null  $endpoint
     * @param  \AgenceCyberial\GraphqlClient\Classes\Factory  $factory
     * @param  array  $callbacks
     * @return void
     */
    public function __construct($endpoint, $factory, $callbacks)
    {
        parent::__construct($endpoint);
        $this->factory = $factory;
        $this->callbacks = $callbacks;
    }

    /**
     * Execute request
     *
     * @return array
     */
    public function makeRequest(string $format, bool $rawResponse = false)
    {
        $this->factory->recordRequest($this);

        $response = $this->stubResponse();

        if ($format == Format::JSON) {
            $response = json_decode(json_encode($response), false);
            if ($rawResponse) return $response;
            return $response->data;
        } else {
            $response = json_decode(json_encode($response), true);
            if ($rawResponse) return $response;
            return Arr::get($response, "data");
        }
    }

    /**
     * Get the stubbed response.
     *
     * @return mixed
     */
    protected function stubResponse()
    {
        $normalizedQuery = $this->normalizeQuery($this->getRawQueryAttribute());

        foreach ($this->callbacks as $key => $callback) {
            // Check if key is a URL pattern
            if ($key === '*' || Str::is($key, $this->endpoint)) {
                return $callback($this);
            }

            // Check if key is a Query pattern (normalized)
            if ($this->normalizeQuery($key) === $normalizedQuery) {
                return $callback($this);
            }
        }

        return [];
    }

    /**
     * Normalize a GraphQL query by removing whitespace.
     *
     * @param  string  $query
     * @return string
     */
    protected function normalizeQuery($query)
    {
        return preg_replace('/\s+/', '', $query);
    }
}
