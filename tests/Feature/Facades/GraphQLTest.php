<?php

namespace AgenceCyberial\GraphqlClient\Tests\Feature\Facades;

use AgenceCyberial\GraphqlClient\Facades\GraphQL;
use AgenceCyberial\GraphqlClient\Tests\TestCase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

class GraphQLTest extends TestCase
{
    public function testGraphQLClient() {
        Http::fake([
            'localhost*' => function (Request $request) {
            if(! $request->isJson()) {
                return null;
            }

            if( ! key_exists('query', $request->data())) {
                return null;
            }

            if($request->data()['query'] !== "query {entity {
          email
          name
         }}") {
                return null;
            }
                return Http::response([
                    'data' => [
                        'entity' => [
                            'email' => 'my@email.com',
                            'name' => 'John Doe',
                        ]
                    ]
                ]);
            }
        ]);
        $response = GraphQL::endpoint("http://localhost/endpoint")->query('entity {
          email
          name
         }')->get();

        $this->assertArrayHasKey('entity', $response);
    }
}