<?php

namespace Tests\Integration\GraphQL;

class UserTest extends TestCase
{
    public function testGetUser()
    {
        $this->be($this->user, 'api');

        $this->graphQL(/** @lang GraphQL */ "
            {
                me {
                    id
                    name
                }
            }
        ")->assertJson([
            'data' => [
                'me' => [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                ],
            ],
        ]);
    }
}
