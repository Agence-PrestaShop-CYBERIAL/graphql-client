<?php

namespace AgenceCyberial\GraphqlClient\Tests\Feature\Facades;

use AgenceCyberial\GraphqlClient\Facades\GraphQL;
use AgenceCyberial\GraphqlClient\Tests\TestCase;
use AgenceCyberial\GraphqlClient\Classes\Client;

class GraphQLFakeTest extends TestCase
{
    public function testFakeReturnsStubbedResponse()
    {
        GraphQL::fake([
            '*' => function () {
                return [
                    'data' => [
                        'user' => [
                            'id' => 1,
                            'name' => 'John Doe',
                        ],
                    ],
                ];
            },
        ]);

        $response = GraphQL::query('
            query {
                user {
                    id
                    name
                }
            }
        ')->get();

        $this->assertEquals([
            'user' => [
                'id' => 1,
                'name' => 'John Doe',
            ],
        ], $response);
    }

    public function testAssertSent()
    {
        GraphQL::fake();

        GraphQL::query('
            query {
                user {
                    id
                    name
                }
            }
        ')->get();

        GraphQL::assertSent(function (Client $client) {
            return $client->queryType === 'query';
        });
    }

    public function testAssertNotSent()
    {
        GraphQL::fake();

        GraphQL::assertNotSent(function (Client $client) {
            return $client->queryType === 'mutation';
        });
    }

    public function testAssertNothingSent()
    {
        GraphQL::fake();

        GraphQL::assertNothingSent();
    }

    public function testAssertSentCount()
    {
        GraphQL::fake();

        GraphQL::query('...')->get();
        GraphQL::query('...')->get();

        GraphQL::assertSentCount(2);
    }
}
