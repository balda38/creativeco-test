<?php

namespace Tests\Integration\GraphQL;

class UserTest extends TestCase
{
    public function testGetUser()
    {
        $this->be($this->user, 'api');

        $response = $this->graphQL(/** @lang GraphQL */ "
            {
                me {
                    id
                    name
                }
            }
        ");
        $this->assertSame($response->json(), [
            'data' => [
                'me' => [
                    'id' => (string) $this->user->id,
                    'name' => $this->user->name,
                ],
            ],
        ]);
    }
}
