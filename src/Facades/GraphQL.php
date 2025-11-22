<?php

namespace AgenceCyberial\GraphqlClient\Facades;
use AgenceCyberial\GraphqlClient\Classes\Client;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Client query(string $query)
 * @method static Client mutation(string $query)
 * @method static Client raw(string $query)
 * @method static Client endpoint(string $endpoint)
 * @method static Client withHeaders(array $headers)
 * @method static \AgenceCyberial\GraphqlClient\Classes\Factory fake(array|callable|null $callback = null)
 * @method static void assertSent(callable|int $callback)
 * @method static void assertNotSent(callable $callback)
 * @method static void assertNothingSent()
 * @method static void assertSentCount(int $count)
 * @method static \Illuminate\Support\Collection recorded(callable $callback)
 */
class GraphQL extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'graphqlClient';
    }
}