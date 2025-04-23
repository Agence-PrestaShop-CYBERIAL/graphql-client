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

    public function testGraphQLClientWithVariables() {
        Http::fake([
            'localhost*' => function (Request $request) {
                if(! $request->isJson()) {
                    return null;
                }

                if( ! key_exists('query', $request->data())) {
                    return null;
                }

                if($request->data()['query'] !== "query createUser (\$email: String!, \$firstname: String!, \$lastname: String!, \$password: String!) {
		createUser(email: \$email, firstname: \$firstname, passwd: \$password, lastname: \$lastname) {
			email
			firstname
			lastname
			active
	}
}") {
                    return null;
                }

                if(! key_exists('variables', $request->data())) {
                    return null;
                }


                if(array_diff([
                    'email' => 'my@email.com',
                    'firstname' => 'John',
                    'lastname' => 'Doe',
                    'passwd' => 'password',
                ], $request->data()['variables']) !== []) {
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
        $response = GraphQL::endpoint("http://localhost/endpoint")->raw('query createUser ($email: String!, $firstname: String!, $lastname: String!, $password: String!) {
		createUser(email: $email, firstname: $firstname, passwd: $password, lastname: $lastname) {
			email
			firstname
			lastname
			active
	}
}')->with([
            'email' => 'my@email.com',
            'firstname' => 'John',
            'lastname' => 'Doe',
            'passwd' => 'password',
        ])->get();;

        $this->assertArrayHasKey('entity', $response);
    }
}