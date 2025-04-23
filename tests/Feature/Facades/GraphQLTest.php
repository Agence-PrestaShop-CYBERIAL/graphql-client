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

                $this->assertSame("POST", $request->method());

                $this->assertTrue($request->isJson());

                $this->assertArrayHasKey('query', $request->data());

                $this->assertArrayHasKey('variables', $request->data());

                $this->assertSame("query {entity {
          email
          name
         }}", $request->data()['query']);

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

                $this->assertSame("POST", $request->method());

                $this->assertTrue($request->isJson());

                $this->assertArrayHasKey('query', $request->data());

                $this->assertArrayHasKey('variables', $request->data());

                $this->assertSame("query createUser (\$email: String!, \$firstname: String!, \$lastname: String!, \$password: String!) {
		createUser(email: \$email, firstname: \$firstname, passwd: \$password, lastname: \$lastname) {
			email
			firstname
			lastname
			active
	}
}", $request->data()['query']);

                $this->assertSame([
                    'email' => 'my@email.com',
                    'firstname' => 'John',
                    'lastname' => 'Doe',
                    'passwd' => 'password',
                ], $request->data()['variables']);
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
        ])->get();

        $this->assertArrayHasKey('entity', $response);
    }

    public function testGraphQLClientWithHeaders()
    {
        Http::fake([
            'localhost*' => function (Request $request) {

                $this->assertSame("POST", $request->method());

                $this->assertTrue($request->isJson());

                $this->assertArrayHasKey('Authorization', $request->headers());
                $this->assertSame(['my-token'], $request->header('Authorization'));

                $this->assertArrayHasKey('Referer', $request->headers());
                $this->assertSame(['my-referer'], $request->header('Referer'));

                $this->assertArrayHasKey('query', $request->data());

                $this->assertArrayHasKey('variables', $request->data());

                $this->assertSame("query {entity {
          email
          name
         }}", $request->data()['query']);

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
         }')
            ->withHeaders([
                'Authorization' => 'my-token',
                'Referer' => 'my-referer',
            ])
            ->get();

        $this->assertArrayHasKey('entity', $response);
    }

    public function testGraphQLClientWithHeader()
    {
        Http::fake([
            'localhost*' => function (Request $request) {

                $this->assertSame("POST", $request->method());

                $this->assertTrue($request->isJson());

                $this->assertArrayHasKey('Authorization', $request->headers());
                $this->assertSame(['my-token'], $request->header('Authorization'));

                $this->assertArrayHasKey('query', $request->data());

                $this->assertArrayHasKey('variables', $request->data());

                $this->assertSame("query {entity {
          email
          name
         }}", $request->data()['query']);

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
         }')
            ->header('Authorization', 'my-token')
            ->get();

        $this->assertArrayHasKey('entity', $response);
    }
}