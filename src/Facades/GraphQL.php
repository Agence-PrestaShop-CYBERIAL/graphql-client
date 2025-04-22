<?php

namespace AgenceCyberial\GraphqlClient\Facades;
use Illuminate\Support\Facades\Facade;

class GraphQL extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'graphqlClient';
    }
}