<?php

namespace AgenceCyberial\GraphqlClient\Tests\Feature\Facades;

use AgenceCyberial\GraphqlClient\Facades\GraphQL;
use AgenceCyberial\GraphqlClient\Tests\TestCase;

class GraphQLFakeQueryTest extends TestCase
{
    public function testFakeMatchesQueryIgnoringWhitespace()
    {
        // Define a mock with a specific query (clean formatting)
        GraphQL::fake([
            'query { user { id name } }' => [
                'data' => [
                    'user' => [
                        'id' => 1,
                        'name' => 'John Doe',
                    ],
                ],
            ],
        ]);

        // Execute the query with different whitespace/formatting
        $response = GraphQL::raw('
            query {
                user {
                    id
                    name
                }
            }
        ')->get();

        // This should match the mock
        $this->assertEquals([
            'user' => [
                'id' => 1,
                'name' => 'John Doe',
            ],
        ], $response);
    }
}
