<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\Currency;
use App\Models\UserAccount;
use App\Models\UserAccountBuyTask;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAccountTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var User
     * @psalm-ignore-var
     */
    private $user;
    /**
     * @var Currency
     * @psalm-ignore-var
     */
    private $currency1;
    /**
     * @var Currency
     * @psalm-ignore-var
     */
    private $currency2;
    /**
     * @var UserAccount
     * @psalm-ignore-var
     */
    private $userAccount;
    /**
     * @var UserAccount
     * @psalm-ignore-var
     */
    private $goalUserAccount;
    /**
     * @var UserAccountBuyTask
     * @psalm-ignore-var
     */
    private $userAccountBuyTask;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->currency1 = Currency::factory()->create();
        $this->currency2 = Currency::factory()->create();
        $this->userAccount = UserAccount::factory()->create([
            'user_id' => $this->user->id,
            'currency_id' => $this->currency1->id,
        ]);
        $this->goalUserAccount = UserAccount::factory()->create([
            'user_id' => $this->user->id,
            'currency_id' => $this->currency2->id,
        ]);
        $this->userAccountBuyTask = UserAccountBuyTask::factory()->create([
            'user_account_id' => $this->userAccount->id,
            'goal_user_account_id' => $this->goalUserAccount->id,
        ]);
    }

    protected function tearDonw(): void
    {
        parent::tearDown();

        $this->user = null;
        $this->currency1 = null;
        $this->currency2 = null;
        $this->userAccount = null;
        $this->goalUserAccount = null;
        $this->userAccountBuyTask = null;
    }

    public function testUser()
    {
        $this->assertTrue($this->userAccount->user->is($this->user));
    }

    public function testCurrency()
    {
        $this->assertTrue($this->userAccount->currency->is($this->currency1));
    }

    public function testOutgoingBuyTasks()
    {
        $this->assertCount(1, $this->userAccount->outgoingBuyTasks);
        $this->assertTrue($this->userAccount->outgoingBuyTasks->first()->is($this->userAccountBuyTask));
    }

    public function testIncomingBuyTasks()
    {
        $this->assertCount(1, $this->goalUserAccount->incomingBuyTasks);
        $this->assertTrue($this->goalUserAccount->incomingBuyTasks->first()->is($this->userAccountBuyTask));
    }

    public function testScopeForUser()
    {
        $account = UserAccount::forUser($this->user)->first();
        $this->assertTrue($account->is($this->userAccount));
        $this->assertEquals($account->user_id, $this->user->id);
    }

    public function testScopeForCurrency()
    {
        $account = UserAccount::forCurrency($this->currency1)->first();
        $this->assertTrue($account->is($this->userAccount));
        $this->assertEquals($account->currency_id, $this->currency1->id);
    }

    public function testGetOwner()
    {
        $this->assertTrue($this->userAccount->getOwner()->is($this->user));
    }
}
