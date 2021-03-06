<?php

namespace Tests\Integration\GraphQL;

use App\Models\User;

use Nuwave\Lighthouse\Testing\ClearsSchemaCache;
use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Tests\TestCase as BaseTestCase;
use Tests\CreatesApplication;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;
    use CreatesApplication;
    use ClearsSchemaCache;
    use MakesGraphQLRequests;

    /**
     * @var User
     * @psalm-ignore-var
     */
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bootClearsSchemaCache();

        $this->user = User::factory()->create();
    }

    protected function tearDown(): void
    {
        $this->user = null;
    }
}
