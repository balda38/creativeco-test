<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\Currency;
use App\Models\UserAccount;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var User
     */
    private $user;
    /**
     * @var Currency
     */
    private $currency;
    /**
     * @var UserAccount
     */
    private $userAccount;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->currency = Currency::factory()->create();
        $this->userAccount = UserAccount::factory()->create([
            'user_id' => $this->user->id,
            'currency_id' => $this->currency->id,
        ]);
    }

    protected function tearDonw(): void
    {
        parent::tearDown();

        $this->user = null;
        $this->currency = null;
        $this->userAccount = null;
    }

    public function testUserAccounts()
    {
        $this->assertCount(1, $this->user->accounts);
        $this->assertTrue($this->user->accounts->first()->is($this->userAccount));
    }
}
