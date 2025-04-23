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
 */
class GraphQL extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'graphqlClient';
    }
}